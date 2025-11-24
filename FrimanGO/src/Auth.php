<?php
/**
 * Autenticación de usuarios - MySQL
 */
class Auth {
    public static function register($email, $name, $password) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Verificar si el email ya existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            throw new Exception('El email ya está registrado');
        }
        $stmt->close();

        // Crear usuario
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (email, name, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $email, $name, $hashedPassword);
        
        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            $stmt->close();
            return $userId;
        }
        
        $stmt->close();
        throw new Exception('Error al crear el usuario');
    }

    public static function login($email, $password) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT id, email, name, password FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            return true;
        }

        return false;
    }

    public static function loginOAuth($provider, $oauthId, $email, $name, $avatar = null) {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Buscar usuario existente por OAuth
        $stmt = $conn->prepare("SELECT id, email, name FROM users WHERE oauth_provider = ? AND oauth_id = ?");
        $stmt->bind_param('ss', $provider, $oauthId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            // Usuario existe, actualizar y login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            return true;
        }

        // Buscar por email
        $stmt = $conn->prepare("SELECT id, email, name FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user) {
            // Actualizar con OAuth
            $stmt = $conn->prepare("UPDATE users SET oauth_provider = ?, oauth_id = ?, avatar = ? WHERE id = ?");
            $stmt->bind_param('sssi', $provider, $oauthId, $avatar, $user['id']);
            $stmt->execute();
            $stmt->close();
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            return true;
        }

        // Crear nuevo usuario OAuth
        $stmt = $conn->prepare("INSERT INTO users (email, name, password, oauth_provider, oauth_id, avatar) VALUES (?, ?, ?, ?, ?, ?)");
        $dummyPassword = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
        $stmt->bind_param('ssssss', $email, $name, $dummyPassword, $provider, $oauthId, $avatar);
        
        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            $stmt->close();
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            return true;
        }
        
        $stmt->close();
        return false;
    }

    public static function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        session_destroy();
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function getCurrentUser() {
        if (!self::isLoggedIn()) return null;
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? ''
        ];
    }
}
