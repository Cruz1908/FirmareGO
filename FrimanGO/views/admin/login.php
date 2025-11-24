<?php
/**
 * Login de Administrador
 */
$pageTitle = 'Admin - Login';
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        if (Auth::login($email, $password)) {
            $user = Auth::getCurrentUser();
            // Verificar si es admin (por ahora todos los usuarios pueden ser admin)
            // En producción, añadir campo is_admin a la tabla users
            header('Location: /admin');
            exit;
        } else {
            $error = 'Email o contraseña incorrectos';
        }
    } else {
        $error = 'Por favor, completa todos los campos';
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/styles.css">
    <style>
        body {
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .admin-login {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .admin-login h1 {
            margin-bottom: 8px;
            color: var(--color-dark);
        }
        .admin-login .subtitle {
            color: var(--color-text-muted);
            margin-bottom: 32px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--color-dark);
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(255, 210, 0, 0.1);
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: var(--color-primary);
            color: var(--color-dark);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: var(--color-primary-dark);
        }
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #c33;
        }
        .info {
            margin-top: 20px;
            padding: 12px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="admin-login">
        <h1>🔐 Admin</h1>
        <p class="subtitle">Panel de administración FrimanGO</p>
        
        <?php if ($error): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-primary">Iniciar sesión</button>
        </form>
        
        <div class="info">
            <strong>Usuario por defecto:</strong><br>
            Email: admin@frimango.com<br>
            Contraseña: admin123
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="/" style="color: var(--color-text-muted); text-decoration: none; font-size: 14px;">← Volver al sitio</a>
        </div>
    </div>
</body>
</html>

