<?php
/**
 * Gestión de Pedidos - Panel Admin
 */
if (!Auth::isLoggedIn()) {
    header('Location: /admin/login');
    exit;
}

$pageTitle = 'Admin - Pedidos';

// Obtener pedidos
$orders = [];
try {
    $db = Database::getInstance();
    if ($db->isConnected()) {
        $conn = $db->getConnection();
        $conn->select_db(DB_NAME);
        
        $result = $conn->query("SELECT o.*, u.name as user_name, u.email as user_email 
                                FROM orders o 
                                LEFT JOIN users u ON o.user_id = u.id 
                                ORDER BY o.created_at DESC 
                                LIMIT 100");
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
    }
} catch (Exception $e) {
    error_log("Error obteniendo pedidos: " . $e->getMessage());
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }
        .admin-actions-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .admin-actions-top h2 {
            margin: 0;
        }
        .orders-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
            color: var(--color-dark);
        }
        .status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
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
        <div class="admin-actions-top">
            <h2>Pedidos (<?= count($orders) ?>)</h2>
        </div>
        
        <div class="orders-table">
            <?php if (empty($orders)): ?>
                <div style="padding: 40px; text-align: center; color: var(--color-text-muted);">
                    <p>No hay pedidos en la base de datos.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['user_name'] ?? $order['name'] ?? 'Cliente') ?></td>
                            <td><?= htmlspecialchars($order['user_email'] ?? $order['email'] ?? '-') ?></td>
                            <td><?= number_format($order['total'], 2, ',', '.') ?> €</td>
                            <td>
                                <span class="status status-<?= strtolower($order['status'] ?? 'pending') ?>">
                                    <?= htmlspecialchars(ucfirst($order['status'] ?? 'pending')) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            <td>
                                <a href="/admin/orders/view?id=<?= $order['id'] ?>" style="color: #3b82f6; text-decoration: none;">Ver</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

