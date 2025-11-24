<?php
/**
 * Panel de administración principal
 */
if (!Auth::isLoggedIn()) {
    header('Location: /admin/login');
    exit;
}

$pageTitle = 'Admin - Panel';
$db = Database::getInstance();

// Obtener estadísticas
$stats = [];
try {
    $conn = $db->getConnection();
    $conn->select_db(DB_NAME);
    
    // Contar productos
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    if ($result) {
        $stats['products'] = $result->fetch_assoc()['total'];
    }
    
    // Contar categorías
    $result = $conn->query("SELECT COUNT(*) as total FROM categories");
    if ($result) {
        $stats['categories'] = $result->fetch_assoc()['total'];
    }
    
    // Contar pedidos
    $result = $conn->query("SELECT COUNT(*) as total FROM orders");
    if ($result) {
        $stats['orders'] = $result->fetch_assoc()['total'];
    }
    
    // Contar usuarios
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    if ($result) {
        $stats['users'] = $result->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
    $stats = ['products' => 0, 'categories' => 0, 'orders' => 0, 'users' => 0];
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
            margin: 0;
        }
        .admin-header {
            background: var(--color-dark);
            color: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            margin: 0;
            font-size: 20px;
        }
        .admin-nav {
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .admin-nav a:hover {
            background: rgba(255,255,255,0.1);
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: var(--color-dark);
            margin: 0;
        }
        .admin-actions {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .admin-actions h2 {
            margin: 0 0 20px 0;
        }
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        .action-btn {
            display: block;
            padding: 16px;
            background: var(--color-primary);
            color: var(--color-dark);
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            transition: background 0.2s;
        }
        .action-btn:hover {
            background: var(--color-primary-dark);
        }
        .action-btn.secondary {
            background: var(--color-border);
            color: var(--color-dark);
        }
        .action-btn.secondary:hover {
            background: #e5e7eb;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1>⚙️ Panel de Administración</h1>
        <nav class="admin-nav">
            <a href="/admin">Dashboard</a>
            <a href="/admin/products">Productos</a>
            <a href="/admin/categories">Categorías</a>
            <a href="/admin/orders">Pedidos</a>
            <a href="/">Ver sitio</a>
            <a href="/api/logout.php">Salir</a>
        </nav>
    </header>
    
    <div class="admin-container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Productos</h3>
                <p class="number"><?= number_format($stats['products'] ?? 0) ?></p>
            </div>
            <div class="stat-card">
                <h3>Categorías</h3>
                <p class="number"><?= number_format($stats['categories'] ?? 0) ?></p>
            </div>
            <div class="stat-card">
                <h3>Pedidos</h3>
                <p class="number"><?= number_format($stats['orders'] ?? 0) ?></p>
            </div>
            <div class="stat-card">
                <h3>Usuarios</h3>
                <p class="number"><?= number_format($stats['users'] ?? 0) ?></p>
            </div>
        </div>
        
        <div class="admin-actions">
            <h2>Acciones rápidas</h2>
            <div class="action-grid">
                <a href="/admin/products/add" class="action-btn">➕ Agregar Producto</a>
                <a href="/admin/products" class="action-btn secondary">📦 Ver Productos</a>
                <a href="/admin/categories" class="action-btn secondary">🏷️ Categorías</a>
                <a href="/admin/orders" class="action-btn secondary">📋 Pedidos</a>
            </div>
        </div>
    </div>
</body>
</html>

