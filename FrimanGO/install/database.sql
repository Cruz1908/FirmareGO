-- ==========================================
-- FrimanGO - Script de Instalación MySQL COMPLETO
-- Base de datos nueva con productos variados
-- ==========================================

-- NOTA: CREATE DATABASE y USE se reemplazan automáticamente en install.php

-- ==========================================
-- TABLA: users
-- Usuarios del sistema
-- ==========================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    oauth_provider VARCHAR(50) DEFAULT NULL,
    oauth_id VARCHAR(255) DEFAULT NULL,
    avatar VARCHAR(500) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLA: categories
-- Categorías de productos
-- ==========================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    name_ca VARCHAR(255) NOT NULL,
    type ENUM('frozen', 'ambient', 'refrigerated') NOT NULL,
    parent_id INT DEFAULT NULL,
    icon VARCHAR(255) DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLA: products
-- Productos del catálogo
-- ==========================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_ca VARCHAR(255) DEFAULT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT DEFAULT NULL,
    description_ca TEXT DEFAULT NULL,
    price DECIMAL(10, 2) NOT NULL,
    compare_price DECIMAL(10, 2) DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    images TEXT DEFAULT NULL COMMENT 'JSON array de imágenes',
    category VARCHAR(50) NOT NULL,
    subcategory VARCHAR(100) DEFAULT NULL,
    category_id INT DEFAULT NULL,
    stock INT DEFAULT 100,
    unit VARCHAR(20) DEFAULT 'kg',
    weight DECIMAL(10, 2) DEFAULT NULL,
    featured BOOLEAN DEFAULT 0,
    active BOOLEAN DEFAULT 1,
    sku VARCHAR(100) UNIQUE DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category),
    INDEX idx_subcategory (subcategory),
    INDEX idx_featured (featured),
    INDEX idx_active (active),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLA: orders
-- Órdenes de compra
-- ==========================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT DEFAULT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    total DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_id VARCHAR(255) DEFAULT NULL,
    shipping_name VARCHAR(255) NOT NULL,
    shipping_email VARCHAR(255) NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    shipping_postal_code VARCHAR(20) NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_order_number (order_number),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLA: order_items
-- Items de cada orden
-- ==========================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================
-- TABLA: sessions
-- Sesiones de usuarios (opcional, mejor usar PHP sessions)
-- ==========================================
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    last_activity DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- INSERTAR CATEGORÍAS (actualizadas)
-- ==========================================
INSERT INTO categories (code, name, name_ca, type, parent_id, sort_order) VALUES
('refrigerats', 'Refrigerated', 'REFRIGERATS', 'refrigerated', NULL, 1),
('peix_refrigerat', 'Fresh Fish', 'PEIX I MARISC REFRIGERATS', 'refrigerated', 1, 2),
('carns_fresques', 'Fresh Meat', 'CARNS FRESQUES', 'refrigerated', 1, 3),
('derivats_lactics', 'Dairy', 'DERIVATS LACTICS', 'refrigerated', 1, 4),
('verdures', 'Vegetables', 'VERDURES FRESQUES', 'refrigerated', 1, 5),
('congelat', 'Frozen', 'CONGELAT', 'frozen', NULL, 10),
('peix_congelat', 'Frozen Fish', 'PEIX CONGELAT', 'frozen', 6, 11),
('carns_congelat', 'Frozen Meat', 'CARNS CONGELAT', 'frozen', 6, 12),
('verduras_congelat', 'Frozen Vegetables', 'VERDURES CONGELATS', 'frozen', 6, 13),
('ambient', 'Ambient', 'AMBIENT', 'ambient', NULL, 20),
('conserves', 'Canned Goods', 'CONSERVES', 'ambient', 10, 21),
('begudes', 'Drinks', 'BEGUDES', 'ambient', 10, 22)
ON DUPLICATE KEY UPDATE name_ca=VALUES(name_ca);

-- ==========================================
-- INSERTAR PRODUCTOS (MINIMO 15 PRODUCTOS VARIADOS)
-- ==========================================

-- NOTA: Los category_id se calcularán basándose en el orden de inserción de categorías
-- Las categorías se insertan en orden, así que los IDs serán: 1=refrigerats, 2=peix_refrigerat, 3=carns_fresques, etc.

-- PESCADOS FRESCOS (4 productos) - category_id = 2 (peix_refrigerat)
INSERT INTO products (name, name_ca, slug, description_ca, price, compare_price, category, subcategory, category_id, unit, stock, featured, active, image) VALUES
('Salmón fresco', 'Salmó fresc', 'salmo-fresc', 'Salmó fresc de primera qualitat', 24.99, 29.99, 'refrigerated', 'peix_refrigerat', 2, 'kg', 100, 1, 1, 'https://images.unsplash.com/photo-1574781330855-d0db8cc6a79d?w=800&h=600&fit=crop'),
('Atún rojo', 'Tonyina vermella', 'tonyina-vermella', 'Tonyina vermella fresca', 32.50, 38.00, 'refrigerated', 'peix_refrigerat', 2, 'kg', 50, 1, 1, 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=800&h=600&fit=crop'),
('Merluza', 'Lluç', 'lluc', 'Lluç fresc de la costa', 12.99, 15.99, 'refrigerated', 'peix_refrigerat', 2, 'kg', 150, 1, 1, 'https://images.unsplash.com/photo-1544947398-0e1ce38c5f69?w=800&h=600&fit=crop'),
('Dorada', 'Orada', 'orada', 'Orada fresca del Mediterrani', 15.99, 18.99, 'refrigerated', 'peix_refrigerat', 2, 'kg', 80, 0, 1, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop')
ON DUPLICATE KEY UPDATE name_ca=VALUES(name_ca), image=VALUES(image);

-- CÁRNICOS FRESCOS (3 productos) - category_id = 3 (carns_fresques)
INSERT INTO products (name, name_ca, slug, description_ca, price, compare_price, category, subcategory, category_id, unit, stock, featured, active, image) VALUES
('Pechuga de pollo', 'Pit de pollastre', 'pit-pollastre', 'Pit de pollastre ecològic', 8.99, 10.99, 'refrigerated', 'carns_fresques', 3, 'kg', 120, 1, 1, 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=800&h=600&fit=crop'),
('Carne picada de ternera', 'Carn picada de vedella', 'carn-picada-vedella', 'Carn picada de vedella primera', 12.99, 14.99, 'refrigerated', 'carns_fresques', 3, 'kg', 90, 0, 1, 'https://images.unsplash.com/photo-1558030006-450675393462?w=800&h=600&fit=crop'),
('Lomo de cerdo', 'Llom de porc', 'llom-porc', 'Llom de porc ibèric', 16.50, 19.50, 'refrigerated', 'carns_fresques', 3, 'kg', 60, 0, 1, 'https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=800&h=600&fit=crop')
ON DUPLICATE KEY UPDATE name_ca=VALUES(name_ca), image=VALUES(image);

-- VERDURAS FRESCAS (2 productos) - category_id = 5 (verdures)
INSERT INTO products (name, name_ca, slug, description_ca, price, compare_price, category, subcategory, category_id, unit, stock, featured, active, image) VALUES
('Tomates ecológicos', 'Tomàquets ecològics', 'tomàquets-ecològics', 'Tomàquets ecològics de la zona', 4.99, 6.99, 'refrigerated', 'verdures', 5, 'kg', 200, 0, 1, 'https://images.unsplash.com/photo-1546470427-e26264be0f42?w=800&h=600&fit=crop'),
('Lechuga iceberg', 'Enciam iceberg', 'enciam-iceberg', 'Enciam iceberg fresc', 1.99, 2.49, 'refrigerated', 'verdures', 5, 'unidad', 300, 0, 1, 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?w=800&h=600&fit=crop')
ON DUPLICATE KEY UPDATE name_ca=VALUES(name_ca), image=VALUES(image);

-- LÁCTEOS (2 productos) - category_id = 4 (derivats_lactics)
INSERT INTO products (name, name_ca, slug, description_ca, price, compare_price, category, subcategory, category_id, unit, stock, featured, active, image) VALUES
('Leche entera', 'Llet sencera', 'llet-sencera', 'Llet sencera pasteuritzada', 1.29, 1.49, 'refrigerated', 'derivats_lactics', 4, 'litro', 250, 0, 1, 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=800&h=600&fit=crop'),
('Queso manchego', 'Formatge manxec', 'formatge-manxec', 'Formatge manxec curat 6 mesos', 12.99, 15.99, 'refrigerated', 'derivats_lactics', 4, 'kg', 70, 0, 1, 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=800&h=600&fit=crop')
ON DUPLICATE KEY UPDATE name_ca=VALUES(name_ca), image=VALUES(image);

-- PESCADO CONGELADO (2 productos) - category_id = 7 (peix_congelat)
INSERT INTO products (name, name_ca, slug, description_ca, price, compare_price, category, subcategory, category_id, unit, stock, featured, active, image) VALUES
('Gambas congeladas', 'Gambes congelades', 'gambes-congelades', 'Gambes congelades talla mitjana', 18.99, 22.99, 'frozen', 'peix_congelat', 7, 'kg', 100, 0, 1, 'https://images.unsplash.com/photo-1606925797300-0b35e9d1794e?w=800&h=600&fit=crop'),
('Merluza congelada', 'Lluç congelat', 'lluc-congelat', 'Lluç congelat en rodanxes', 9.99, 12.99, 'frozen', 'peix_congelat', 7, 'kg', 150, 0, 1, 'https://images.unsplash.com/photo-1544947398-0e1ce38c5f69?w=800&h=600&fit=crop')
ON DUPLICATE KEY UPDATE name_ca=VALUES(name_ca), image=VALUES(image);

-- VERDURAS CONGELADAS (2 productos) - category_id = 9 (verduras_congelat)
INSERT INTO products (name, name_ca, slug, description_ca, price, compare_price, category, subcategory, category_id, unit, stock, featured, active, image) VALUES
('Guisantes congelados', 'Pèsols congelats', 'pèsols-congelats', 'Pèsols congelats naturals', 3.99, 4.99, 'frozen', 'verduras_congelat', 9, 'kg', 180, 0, 1, 'https://images.unsplash.com/photo-1518977822534-7049a61ee0c2?w=800&h=600&fit=crop'),
('Espinacas congeladas', 'Espinacs congelats', 'espinacs-congelats', 'Espinacs congelats en fulls', 2.99, 3.99, 'frozen', 'verduras_congelat', 9, 'kg', 160, 0, 1, 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?w=800&h=600&fit=crop')
ON DUPLICATE KEY UPDATE name_ca=VALUES(name_ca), image=VALUES(image);

-- PRODUCTOS AMBIENT (2 productos) - category_id = 11 (conserves)
INSERT INTO products (name, name_ca, slug, description_ca, price, compare_price, category, subcategory, category_id, unit, stock, featured, active, image) VALUES
('Atún en conserva', 'Tonyina en conserva', 'tonyina-conserva', 'Tonyina en conserva d''oliva', 3.49, 4.49, 'ambient', 'conserves', 11, 'lata', 200, 0, 1, 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=800&h=600&fit=crop'),
('Aceite de oliva virgen', 'Oli d''oliva verge', 'oli-oliva-verge', 'Oli d''oliva verge extra primera premsada', 8.99, 11.99, 'ambient', 'conserves', 11, 'litro', 120, 0, 1, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=800&h=600&fit=crop')
ON DUPLICATE KEY UPDATE name_ca=VALUES(name_ca), image=VALUES(image);


