<?php
/**
 * Editar producto - Panel Admin
 */
if (!Auth::isLoggedIn()) {
    header('Location: /admin/login');
    exit;
}

$pageTitle = 'Admin - Editar Producto';
$productId = intval($_GET['id'] ?? 0);
$product = null;

if ($productId > 0) {
    try {
        $product = Product::getById($productId);
    } catch (Exception $e) {
        error_log("Error obteniendo producto: " . $e->getMessage());
    }
}

if (!$product) {
    header('Location: /admin/products');
    exit;
}

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $name_ca = $_POST['name_ca'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $description = $_POST['description'] ?? '';
    $description_ca = $_POST['description_ca'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $category = $_POST['category'] ?? '';
    $subcategory = $_POST['subcategory'] ?? '';
    $unit = $_POST['unit'] ?? 'kg';
    $stock = intval($_POST['stock'] ?? 100);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $active = isset($_POST['active']) ? 1 : 1;
    $image = $_POST['image'] ?? '';
    
    if (empty($name) || empty($slug) || empty($price) || empty($category)) {
        $error = 'Por favor, completa todos los campos obligatorios';
    } else {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            $conn->select_db(DB_NAME);
            
            // Verificar si existe columna active
            $checkActive = $conn->query("SHOW COLUMNS FROM products LIKE 'active'");
            $hasActive = ($checkActive !== false && $checkActive->num_rows > 0);
            
            if ($hasActive) {
                $stmt = $conn->prepare("UPDATE products SET name = ?, name_ca = ?, slug = ?, description = ?, description_ca = ?, price = ?, category = ?, subcategory = ?, unit = ?, stock = ?, featured = ?, active = ?, image = ? WHERE id = ?");
                $stmt->bind_param('sssssdsssiissi', $name, $name_ca, $slug, $description, $description_ca, $price, $category, $subcategory, $unit, $stock, $featured, $active, $image, $productId);
            } else {
                $stmt = $conn->prepare("UPDATE products SET name = ?, name_ca = ?, slug = ?, description = ?, description_ca = ?, price = ?, category = ?, subcategory = ?, unit = ?, stock = ?, featured = ?, image = ? WHERE id = ?");
                $stmt->bind_param('sssssdsssiisi', $name, $name_ca, $slug, $description, $description_ca, $price, $category, $subcategory, $unit, $stock, $featured, $image, $productId);
            }
            
            if ($stmt->execute()) {
                header('Location: /admin/products?message=' . urlencode('Producto actualizado correctamente'));
                exit;
            } else {
                $error = 'Error al actualizar producto: ' . $stmt->error;
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
} else {
    // Usar valores del producto
    $_POST = $product;
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
            max-width: 900px;
            margin: 0 auto;
            padding: 24px;
        }
        .admin-form {
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        .form-group label .required {
            color: #ef4444;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(255, 210, 0, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-checkbox input {
            width: auto;
        }
        .btn-primary {
            padding: 12px 24px;
            background: var(--color-primary);
            color: var(--color-dark);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-primary:hover {
            background: var(--color-primary-dark);
        }
        .btn-secondary {
            padding: 12px 24px;
            background: var(--color-border);
            color: var(--color-dark);
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            margin-right: 12px;
        }
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #c33;
        }
        .help-text {
            font-size: 14px;
            color: var(--color-text-muted);
            margin-top: 4px;
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
        <div class="admin-form">
            <h2>Editar Producto</h2>
            
            <?php if ($error): ?>
                <div class="alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nombre <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="name_ca">Nombre (Catalán)</label>
                        <input type="text" id="name_ca" name="name_ca" value="<?= htmlspecialchars($_POST['name_ca'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="slug">Slug <span class="required">*</span></label>
                    <input type="text" id="slug" name="slug" required value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Precio (€) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="unit">Unidad</label>
                        <select id="unit" name="unit">
                            <option value="kg" <?= ($_POST['unit'] ?? 'kg') === 'kg' ? 'selected' : '' ?>>kg</option>
                            <option value="g" <?= ($_POST['unit'] ?? '') === 'g' ? 'selected' : '' ?>>g</option>
                            <option value="ud" <?= ($_POST['unit'] ?? '') === 'ud' ? 'selected' : '' ?>>ud</option>
                            <option value="litro" <?= ($_POST['unit'] ?? '') === 'litro' ? 'selected' : '' ?>>litro</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Categoría <span class="required">*</span></label>
                        <select id="category" name="category" required>
                            <option value="">Seleccionar...</option>
                            <option value="frozen" <?= ($_POST['category'] ?? '') === 'frozen' ? 'selected' : '' ?>>Congelado</option>
                            <option value="refrigerated" <?= ($_POST['category'] ?? '') === 'refrigerated' ? 'selected' : '' ?>>Refrigerado</option>
                            <option value="ambient" <?= ($_POST['category'] ?? '') === 'ambient' ? 'selected' : '' ?>>Ambiente</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="subcategory">Subcategoría</label>
                        <input type="text" id="subcategory" name="subcategory" value="<?= htmlspecialchars($_POST['subcategory'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" min="0" value="<?= htmlspecialchars($_POST['stock'] ?? '100') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Imagen del Producto</label>
                        <input type="file" id="image-file" name="image_file" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" style="display: none;">
                        <input type="hidden" id="image" name="image" value="<?= htmlspecialchars($_POST['image'] ?? '') ?>">
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <button type="button" onclick="document.getElementById('image-file').click()" class="btn-secondary" style="margin: 0;">📷 Subir Imagen</button>
                            <span id="image-filename" style="font-size: 14px; color: var(--color-text-muted);"></span>
                        </div>
                        <?php if (!empty($_POST['image'])): ?>
                        <div id="image-preview" style="margin-top: 12px;">
                            <img id="preview-img" src="<?= htmlspecialchars($_POST['image']) ?>" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid var(--color-border);">
                        </div>
                        <?php else: ?>
                        <div id="image-preview" style="margin-top: 12px; display: none;">
                            <img id="preview-img" src="" alt="Preview" style="max-width: 300px; max-height: 200px; border-radius: 8px; border: 1px solid var(--color-border);">
                        </div>
                        <?php endif; ?>
                        <p class="help-text">O introduce una URL de imagen</p>
                        <input type="url" id="image-url" placeholder="https://..." value="<?= htmlspecialchars($_POST['image'] ?? '') ?>" style="margin-top: 8px; width: 100%; padding: 12px; border: 1px solid var(--color-border); border-radius: 8px;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea id="description" name="description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="description_ca">Descripción (Catalán)</label>
                    <textarea id="description_ca" name="description_ca"><?= htmlspecialchars($_POST['description_ca'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" id="featured" name="featured" value="1" <?= ($_POST['featured'] ?? 0) ? 'checked' : '' ?>>
                        <label for="featured" style="margin: 0;">Producto destacado</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="form-checkbox">
                        <input type="checkbox" id="active" name="active" value="1" <?= (isset($_POST['active']) ? ($_POST['active'] ?? 1) : 1) ? 'checked' : '' ?>>
                        <label for="active" style="margin: 0;">Activo</label>
                    </div>
                </div>
                
                <div style="margin-top: 32px;">
                    <a href="/admin/products" class="btn-secondary">Cancelar</a>
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Upload de imagen
        const imageFile = document.getElementById('image-file');
        const imageInput = document.getElementById('image');
        const imageUrlInput = document.getElementById('image-url');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const imageFilename = document.getElementById('image-filename');
        
        imageFile.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Validar tipo
            if (!file.type.match('image.*')) {
                alert('Por favor, selecciona un archivo de imagen');
                return;
            }
            
            // Validar tamaño (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 5MB');
                return;
            }
            
            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                imageFilename.textContent = file.name;
            };
            reader.readAsDataURL(file);
            
            // Subir archivo
            const formData = new FormData();
            formData.append('image', file);
            
            try {
                const response = await fetch('/api/admin/products/upload.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    imageInput.value = data.url;
                    imageUrlInput.value = data.url;
                } else {
                    alert('Error al subir imagen: ' + (data.error || 'Error desconocido'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al subir imagen');
            }
        });
        
        // Actualizar imagen desde URL
        imageUrlInput.addEventListener('input', function() {
            const url = this.value.trim();
            if (url) {
                imageInput.value = url;
                previewImg.src = url;
                imagePreview.style.display = 'block';
                imageFilename.textContent = 'URL externa';
            }
        });
    </script>
</body>
</html>

