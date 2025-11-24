<?php
/**
 * Clase para gestión de conexión a base de datos MySQL
 */
class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        if (DB_TYPE === 'mysql') {
            // Primero conectar sin seleccionar base de datos
            $this->connection = @new mysqli(DB_HOST, DB_USER, DB_PASS);
            
            if ($this->connection->connect_error) {
                error_log("Error de conexión MySQL: " . $this->connection->connect_error);
                $this->connection = null;
                return;
            }
            
            // Seleccionar la base de datos
            if (!empty(DB_NAME) && !$this->connection->select_db(DB_NAME)) {
                // Si la base de datos no existe, no es error fatal - las tablas pueden no estar creadas
                error_log("Advertencia: Base de datos '" . DB_NAME . "' no existe o no se pudo seleccionar: " . $this->connection->error);
                // Continuamos igual, el método tablesExist() verificará si las tablas existen
            }
            
            $this->connection->set_charset(DB_CHARSET);
            
            // Habilitar errores en modo desarrollo
            if (!defined('PRODUCTION') || !PRODUCTION) {
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            }
        } else {
            // SQLite (fallback)
            $dbPath = APP_ROOT . '/data/frimango.db';
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $this->connection = new SQLite3($dbPath);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Resetear la instancia (útil después de instalar tablas)
     */
    public static function resetInstance() {
        if (self::$instance !== null && self::$instance->connection !== null) {
            if (DB_TYPE === 'mysql') {
                self::$instance->connection->close();
            }
        }
        self::$instance = null;
    }

    public function getConnection() {
        return $this->connection;
    }
    
    public function isConnected() {
        return $this->connection !== null && !$this->connection->connect_error;
    }

    public function query($sql) {
        if (!$this->isConnected()) {
            error_log("Intento de query sin conexión: " . $sql);
            return false;
        }
        
        if (DB_TYPE === 'mysql') {
            $result = $this->connection->query($sql);
            if ($result === false) {
                error_log("Error en query: " . $this->connection->error . " | SQL: " . $sql);
            }
            return $result;
        } else {
            return $this->connection->query($sql);
        }
    }

    public function prepare($sql) {
        if (!$this->isConnected()) {
            error_log("Intento de prepare sin conexión: " . $sql);
            return false;
        }
        
        $stmt = $this->connection->prepare($sql);
        if ($stmt === false) {
            error_log("Error preparando consulta: " . $this->connection->error . " | SQL: " . $sql);
        }
        return $stmt;
    }

    public function escape($string) {
        if (!$this->isConnected()) {
            return addslashes($string);
        }
        
        if (DB_TYPE === 'mysql') {
            return $this->connection->real_escape_string($string);
        } else {
            return SQLite3::escapeString($string);
        }
    }

    public function lastInsertId() {
        if (!$this->isConnected()) {
            return 0;
        }
        
        if (DB_TYPE === 'mysql') {
            return $this->connection->insert_id;
        } else {
            return $this->connection->lastInsertRowID();
        }
    }

    public function affectedRows() {
        if (!$this->isConnected()) {
            return 0;
        }
        
        if (DB_TYPE === 'mysql') {
            return $this->connection->affected_rows;
        } else {
            return $this->connection->changes();
        }
    }

    public function fetchArray($result, $mode = null) {
        if ($result === false) {
            return false;
        }
        
        if (DB_TYPE === 'mysql') {
            return $result->fetch_array($mode ?? MYSQLI_ASSOC);
        } else {
            return $result->fetchArray($mode ?? SQLITE3_ASSOC);
        }
    }

    public function fetchAll($result) {
        if ($result === false) {
            return [];
        }
        
        $rows = [];
        if (DB_TYPE === 'mysql') {
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $rows[] = $row;
            }
        } else {
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    // No cerrar la conexión en __destruct porque se reutiliza
    public function __destruct() {
        // Mantener la conexión abierta para reutilización
    }
}
