<?php
/**
 * Lista de productos - Panel Admin
 */
if (!Auth::isLoggedIn()) {
    header('Location: /admin/login');
    exit;
}

$pageTitle = 'Admin - Productos';

// Obtener productos
try {
    $products = Product::getAll();
    if (!is_array($products)) {
        $products = [];
    }
} catch (Exception $e) {
    error_log("Error obteniendo productos en admin: " . $e->getMessage());
    $products = [];
}

$message = $_GET['message'] ?? null;
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
        .products-table {
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
        .btn-small {
            padding: 6px 12px;
            font-size: 14px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-right: 8px;
        }
        .btn-edit {
            background: #3b82f6;
            color: white;
        }
        .btn-delete {
            background: #ef4444;
            color: white;
            border: none;
            cursor: pointer;
        }
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #155724;
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
            <h2>Productos (<?= count($products) ?>)</h2>
            <a href="/admin/products/add" class="btn-primary">➕ Agregar Producto</a>
        </div>
        
        <?php if ($message): ?>
            <div class="alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <div class="products-table">
            <?php if (empty($products)): ?>
                <div style="padding: 40px; text-align: center; color: var(--color-text-muted);">
                    <p>No hay productos en la base de datos.</p>
                    <a href="/admin/products/add" class="btn-primary" style="margin-top: 16px;">Agregar primer producto</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Nombre CA</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Featured</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td><?= htmlspecialchars($product['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($product['name_ca'] ?? '') ?></td>
                            <td><?= number_format($product['price'], 2, ',', '.') ?> €</td>
                            <td><?= htmlspecialchars($product['category'] ?? '') ?></td>
                            <td><?= ($product['featured'] ?? 0) ? '✓' : '' ?></td>
                            <td><?= $product['stock'] ?? 0 ?></td>
                            <td>
                                <a href="/admin/products/edit?id=<?= $product['id'] ?>" class="btn-small btn-edit">Editar</a>
                                <button class="btn-small btn-delete" onclick="deleteProduct(<?= $product['id'] ?>)">Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function deleteProduct(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                fetch('/api/admin/products/delete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Error al eliminar'));
                    }
                });
            }
        }
    </script>
</body>
</html>

