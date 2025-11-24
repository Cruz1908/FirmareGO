<?php
/**
 * Modelo de Producto - MySQL
 */
class Product {
    // No requiere inicialización - las tablas se crean mediante install/database.sql

    public static function getAll($category = null, $subcategory = null) {
        try {
            $db = Database::getInstance();
            
            if (!$db->isConnected()) {
                error_log("No hay conexión a la base de datos en getAll");
                return [];
            }
            
            $conn = $db->getConnection();
            
            // Intentar con columna active, si falla usar sin ella
            $query = "SELECT * FROM products WHERE 1=1";
            $params = [];
            $types = '';
            
            // Intentar agregar condición active
            $testStmt = $conn->prepare("SELECT * FROM products WHERE active = 1 LIMIT 1");
            $hasActiveColumn = ($testStmt !== false);
            if ($testStmt) {
                $testStmt->close();
            }
            
            if ($hasActiveColumn) {
                $query = "SELECT * FROM products WHERE active = 1";
            }
            
            if ($category) {
                $query .= " AND category = ?";
                $params[] = $category;
                $types .= 's';
            }
            if ($subcategory) {
                $query .= " AND subcategory = ?";
                $params[] = $subcategory;
                $types .= 's';
            }
            
            $query .= " ORDER BY featured DESC, name ASC";
            
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                error_log("Error preparando consulta getAll: " . $conn->error);
                return [];
            }
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            if (!$stmt->execute()) {
                error_log("Error ejecutando consulta getAll: " . $stmt->error);
                $stmt->close();
                return [];
            }
            
            $result = $stmt->get_result();
            if ($result === false) {
                error_log("Error obteniendo resultado getAll: " . $stmt->error);
                $stmt->close();
                return [];
            }
            
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            $stmt->close();
            return $products;
        } catch (Exception $e) {
            error_log("Excepción en getAll: " . $e->getMessage());
            return [];
        }
    }

    public static function getById($id) {
        try {
            $db = Database::getInstance();
            
            if (!$db->isConnected()) {
                return null;
            }
            
            $conn = $db->getConnection();
            
            // Intentar con active primero
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND active = 1");
            if ($stmt === false) {
                // Si falla, intentar sin active
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
                if ($stmt === false) {
                    error_log("Error preparando consulta getById: " . $conn->error);
                    return null;
                }
            }
            
            $stmt->bind_param('i', $id);
            if (!$stmt->execute()) {
                error_log("Error ejecutando consulta getById: " . $stmt->error);
                $stmt->close();
                return null;
            }
            
            $result = $stmt->get_result();
            if ($result === false) {
                $stmt->close();
                return null;
            }
            
            $product = $result->fetch_assoc();
            $stmt->close();
            
            return $product ?: null;
        } catch (Exception $e) {
            error_log("Excepción en getById: " . $e->getMessage());
            return null;
        }
    }

    public static function getBySlug($slug) {
        try {
            $db = Database::getInstance();
            
            if (!$db->isConnected()) {
                return null;
            }
            
            $conn = $db->getConnection();
            
            // Intentar con active primero
            $stmt = $conn->prepare("SELECT * FROM products WHERE slug = ? AND active = 1");
            if ($stmt === false) {
                // Si falla, intentar sin active
                $stmt = $conn->prepare("SELECT * FROM products WHERE slug = ?");
                if ($stmt === false) {
                    error_log("Error preparando consulta getBySlug: " . $conn->error);
                    return null;
                }
            }
            
            $stmt->bind_param('s', $slug);
            if (!$stmt->execute()) {
                error_log("Error ejecutando consulta getBySlug: " . $stmt->error);
                $stmt->close();
                return null;
            }
            
            $result = $stmt->get_result();
            if ($result === false) {
                $stmt->close();
                return null;
            }
            
            $product = $result->fetch_assoc();
            $stmt->close();
            
            return $product ?: null;
        } catch (Exception $e) {
            error_log("Excepción en getBySlug: " . $e->getMessage());
            return null;
        }
    }

    public static function getCategories() {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            // Verificar que la conexión existe
            if (!$conn) {
                error_log("Error: No hay conexión a la base de datos");
                return [];
            }
            
            $result = $conn->query("SELECT * FROM categories ORDER BY sort_order, name_ca");
            
            // Verificar que la consulta se ejecutó correctamente
            if ($result === false) {
                error_log("Error en consulta getCategories: " . $conn->error);
                // Si la tabla no existe, retornar array vacío
                return [];
            }
            
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            
            return $categories;
        } catch (Exception $e) {
            error_log("Excepción en getCategories: " . $e->getMessage());
            return [];
        }
    }

    public static function getFeatured($limit = 8) {
        try {
            $db = Database::getInstance();
            
            if (!$db->isConnected()) {
                error_log("No hay conexión a la base de datos en getFeatured");
                return [];
            }
            
            $conn = $db->getConnection();
            
            // Asegurarse de que estamos en la base de datos correcta
            if (!empty(DB_NAME)) {
                $conn->select_db(DB_NAME);
            }
            
            // Primero verificar si existe la columna active
            $checkActive = $conn->query("SHOW COLUMNS FROM products LIKE 'active'");
            $hasActiveColumn = ($checkActive !== false && $checkActive->num_rows > 0);
            
            // Construir la consulta según si existe la columna active
            if ($hasActiveColumn) {
                // Si existe active, usarla
                $query = "SELECT * FROM products WHERE featured = 1 AND active = 1 ORDER BY name LIMIT ?";
            } else {
                // Si no existe, solo verificar featured
                $query = "SELECT * FROM products WHERE featured = 1 ORDER BY name LIMIT ?";
            }
            
            // Si no hay productos destacados, obtener los primeros productos
            $stmt = $conn->prepare($query);
            
            if ($stmt === false) {
                error_log("Error preparando consulta getFeatured: " . $conn->error);
                // Si falla, intentar obtener cualquier producto
                $stmt = $conn->prepare("SELECT * FROM products ORDER BY name LIMIT ?");
                if ($stmt === false) {
                    error_log("Error fatal preparando consulta getFeatured: " . $conn->error);
                    return [];
                }
            }
            
            $stmt->bind_param('i', $limit);
            
            if (!$stmt->execute()) {
                error_log("Error ejecutando consulta getFeatured: " . $stmt->error);
                $stmt->close();
                return [];
            }
            
            $result = $stmt->get_result();
            if ($result === false) {
                error_log("Error obteniendo resultado getFeatured: " . $stmt->error);
                $stmt->close();
                return [];
            }
            
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            $stmt->close();
            
            // Si no hay productos destacados pero hay productos en la BD, retornar los primeros
            if (empty($products)) {
                error_log("No hay productos destacados, intentando obtener primeros productos");
                $stmt = $conn->prepare("SELECT * FROM products ORDER BY id ASC LIMIT ?");
                if ($stmt !== false) {
                    $stmt->bind_param('i', $limit);
                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $products[] = $row;
                        }
                    }
                    $stmt->close();
                }
            }
            
            return $products;
        } catch (Exception $e) {
            error_log("Excepción en getFeatured: " . $e->getMessage());
            return [];
        }
    }

    public static function search($query) {
        try {
            $db = Database::getInstance();
            
            if (!$db->isConnected()) {
                return [];
            }
            
            $conn = $db->getConnection();
            
            $searchTerm = '%' . $conn->real_escape_string($query) . '%';
            
            // Intentar con active primero
            $stmt = $conn->prepare("SELECT * FROM products WHERE (name LIKE ? OR name_ca LIKE ? OR description LIKE ?) AND active = 1");
            if ($stmt === false) {
                // Si falla, intentar sin active
                $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR name_ca LIKE ? OR description LIKE ?");
                if ($stmt === false) {
                    error_log("Error preparando consulta search: " . $conn->error);
                    return [];
                }
            }
            
            $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
            if (!$stmt->execute()) {
                error_log("Error ejecutando consulta search: " . $stmt->error);
                $stmt->close();
                return [];
            }
            
            $result = $stmt->get_result();
            if ($result === false) {
                $stmt->close();
                return [];
            }
            
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            
            $stmt->close();
            return $products;
        } catch (Exception $e) {
            error_log("Excepción en search: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verifica si las tablas necesarias existen en la base de datos
     */
    public static function tablesExist() {
        try {
            $db = Database::getInstance();
            
            if (!$db->isConnected()) {
                return false;
            }
            
            $conn = $db->getConnection();
            
            // Asegurarse de que estamos en la base de datos correcta
            if (!empty(DB_NAME)) {
                if (!$conn->select_db(DB_NAME)) {
                    // Si no puede seleccionar la BD, significa que no existe
                    return false;
                }
            }
            
            // Verificar si existe la tabla products (como indicador principal)
            // Usar información_schema es más confiable que SHOW TABLES
            $query = "SELECT COUNT(*) as count FROM information_schema.tables 
                     WHERE table_schema = ? AND table_name = 'products'";
            $stmt = $conn->prepare($query);
            
            if ($stmt === false) {
                // Si falla, intentar método alternativo
                $result = $conn->query("SHOW TABLES LIKE 'products'");
                if ($result === false) {
                    return false;
                }
                return $result->num_rows > 0;
            }
            
            $dbName = DB_NAME;
            $stmt->bind_param('s', $dbName);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result === false) {
                // Fallback al método simple
                $result = $conn->query("SHOW TABLES LIKE 'products'");
                if ($result === false) {
                    return false;
                }
                return $result->num_rows > 0;
            }
            
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return isset($row['count']) && $row['count'] > 0;
        } catch (Exception $e) {
            error_log("Excepción en tablesExist: " . $e->getMessage());
            // Intentar método simple como fallback
            try {
                $db = Database::getInstance();
                if (!$db->isConnected()) {
                    return false;
                }
                $conn = $db->getConnection();
                if (!empty(DB_NAME)) {
                    $conn->select_db(DB_NAME);
                }
                $result = $conn->query("SHOW TABLES LIKE 'products'");
                return $result !== false && $result->num_rows > 0;
            } catch (Exception $e2) {
                return false;
            }
        }
    }
}
