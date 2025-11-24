<?php
/**
 * Gestión de Categorías - Panel Admin
 */
if (!Auth::isLoggedIn()) {
    header('Location: /admin/login');
    exit;
}

$pageTitle = 'Admin - Categorías';

// Obtener categorías
try {
    $categories = Product::getCategories();
    if (!is_array($categories)) {
        $categories = [];
    }
} catch (Exception $e) {
    error_log("Error obteniendo categorías: " . $e->getMessage());
    $categories = [];
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
        .btn-primary {
            padding: 12px 24px;
            background: var(--color-primary);
            color: var(--color-dark);
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: var(--color-primary-dark);
        }
        .categories-table {
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
        .info-box {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 24px;
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
            <h2>Categorías (<?= count($categories) ?>)</h2>
        </div>
        
        <div class="info-box">
            <p><strong>Nota:</strong> Las categorías se gestionan principalmente desde la base de datos.</p>
            <p>Para agregar o modificar categorías, usa phpMyAdmin o ejecuta SQL directamente.</p>
        </div>
        
        <div class="categories-table">
            <?php if (empty($categories)): ?>
                <div style="padding: 40px; text-align: center; color: var(--color-text-muted);">
                    <p>No hay categorías en la base de datos.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Nombre CA</th>
                            <th>Tipo</th>
                            <th>Padre</th>
                            <th>Orden</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?= $category['id'] ?></td>
                            <td><?= htmlspecialchars($category['code'] ?? '') ?></td>
                            <td><?= htmlspecialchars($category['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($category['name_ca'] ?? '') ?></td>
                            <td><?= htmlspecialchars($category['type'] ?? '') ?></td>
                            <td><?= $category['parent_id'] ?? '-' ?></td>
                            <td><?= $category['sort_order'] ?? 0 ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

