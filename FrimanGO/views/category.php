<?php
/**
 * Página de categoría según diseño Category Page de Figma
 */
$pageTitle = 'Categorías';
$dbConnected = false;
$tablesExist = false;

// Obtener categoría del query string
$categoryCode = $_GET['cat'] ?? null;
$searchQuery = $_GET['search'] ?? null;

try {
    $db = Database::getInstance();
    $dbConnected = $db->isConnected();
    if ($dbConnected) {
        $tablesExist = Product::tablesExist();
    }
} catch (Exception $e) {
    error_log("Error verificando conexión en category.php: " . $e->getMessage());
}

// Obtener categorías para el menú lateral
try {
    $allCategories = ($dbConnected && $tablesExist) ? Product::getCategories() : [];
    if (!is_array($allCategories)) {
        $allCategories = [];
    }
} catch (Exception $e) {
    error_log("Error obteniendo categorías en category.php: " . $e->getMessage());
    $allCategories = [];
}

// Obtener productos
try {
    if (!$dbConnected || !$tablesExist) {
        $products = [];
        $categoryTitle = 'Base de datos no configurada';
    } elseif ($searchQuery) {
        // Usar el método search de Product
        $products = Product::search($searchQuery);
        $categoryTitle = 'Resultados de búsqueda: ' . htmlspecialchars($searchQuery);
    } elseif ($categoryCode) {
        $category = array_filter($allCategories, fn($c) => $c['code'] === $categoryCode);
        $category = reset($category);
        if ($category) {
            $products = Product::getAll($category['type'] ?? null, $categoryCode);
            $categoryTitle = Lang::getCategoryName($category) ?? 'Categoría';
        } else {
            $products = [];
            $categoryTitle = 'Categoría no encontrada';
        }
    } else {
        $products = Product::getAll();
        $categoryTitle = 'Todos los productos';
    }
    
    if (!is_array($products)) {
        $products = [];
    }
} catch (Exception $e) {
    error_log("Error obteniendo productos en category.php: " . $e->getMessage());
    $products = [];
    $categoryTitle = 'Error al cargar productos';
}
?>

<section class="category-hero" style="background-image:url('<?= APP_URL ?>/assets/images/Category Page.png')">
  <aside class="left-panel">
    <div class="panel-logo">
      <span class="friman">friman</span>
      <span class="go">GO</span>
      <div class="tagline">CLICK & COLLECT</div>
    </div>
    <div class="panel-separator"></div>
    <div class="panel-content">
      <?php
      $currentType = null;
      foreach ($allCategories as $cat):
        if ($cat['parent_id'] === null):
          if ($currentType !== null) echo '</div>';
          $currentType = $cat['type'];
      ?>
      <div class="panel-section">
        <h4 class="panel-section-title"><?= htmlspecialchars(Lang::getCategoryName($cat)) ?></h4>
        <ul class="panel-list">
        <?php
        else:
          if ($cat['parent_id'] == 1 && strtolower($cat['code']) == 'verduras'):
        ?>
          <li class="panel-item-dash">– <?= CURRENT_LANG === 'es' ? 'VERDURAS' : 'VERDURES' ?></li>
          <ul class="panel-sublist">
            <li><?= CURRENT_LANG === 'es' ? 'FRUTAS CONGELADAS' : 'FRUITE CONGELADES' ?></li>
            <li><?= CURRENT_LANG === 'es' ? 'SOLO REGENERAR' : 'NOMES REGENERAR' ?></li>
            <li><?= CURRENT_LANG === 'es' ? 'PREPARADOS DE PROTEÍNA VEGETAL' : 'PREPARATS DE PROTEINA VEGETAL' ?></li>
            <li><?= CURRENT_LANG === 'es' ? 'VERDURAS AL NATURAL' : 'VERDURES AL NATURAL' ?></li>
            <li><?= CURRENT_LANG === 'es' ? 'VERDURAS PREPARADAS' : 'VERDURES PREPARADES' ?></li>
          </ul>
        <?php else: ?>
          <li class="panel-item">
            <span class="panel-plus">+</span>
            <a href="/category?cat=<?= urlencode($cat['code']) ?>"><?= htmlspecialchars(Lang::getCategoryName($cat)) ?></a>
          </li>
        <?php
          endif;
        endif;
      endforeach;
      if ($currentType !== null) echo '</ul></div>';
      ?>
    </div>
  </aside>
  <div class="category-overlay">
    <div class="container">
      <h2 class="category-title"><?= htmlspecialchars($categoryTitle) ?></h2>
    </div>
  </div>
</section>

<section class="section container">
  <div class="filters">
      <div class="price-filter">
        <div class="filter-title"><?= Lang::get('filter.price') ?></div>
      <div class="price-bubbles">
        <span id="price-min-label">0 €</span>
        <span id="price-max-label">100 €</span>
      </div>
      <div class="range-track">
        <input id="price-min" type="range" min="0" max="100" value="0" class="range-input" />
        <input id="price-max" type="range" min="0" max="100" value="100" class="range-input" />
      </div>
        <button id="price-apply" class="primary" style="margin-top:12px"><?= Lang::get('filter.apply') ?></button>
    </div>
  </div>

  <div id="product-grid" class="grid">
    <?php if (!$dbConnected || !$tablesExist): ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--color-text-muted);">
        <p style="font-size: 18px; margin-bottom: 16px;">Base de datos no configurada</p>
        <p style="font-size: 14px; margin-bottom: 20px;">Es necesario instalar la base de datos para que el sitio funcione correctamente.</p>
        <a href="<?= APP_URL ?>/install/install.php" style="display: inline-block; padding: 12px 24px; background: var(--color-primary); color: var(--color-dark); text-decoration: none; border-radius: 8px; font-weight: 600;">Instalar base de datos</a>
      </div>
    <?php elseif (empty($products)): ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--color-text-muted);">
        <p style="font-size: 18px; margin-bottom: 16px;">No hay productos disponibles</p>
        <p style="font-size: 14px;">No se encontraron productos en esta categoría. Próximamente agregaremos más productos.</p>
      </div>
    <?php else: ?>
      <?php foreach ($products as $product): ?>
      <article class="card">
      <a href="/product?id=<?= $product['id'] ?>" class="card-image">
        <?php if ($product['image']): ?>
          <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
        <?php else: ?>
          <div class="placeholder-image">Pescado</div>
        <?php endif; ?>
      </a>
      <div class="card-body">
        <h3 class="card-title">
          <a href="/product?id=<?= $product['id'] ?>"><?= htmlspecialchars(Lang::getProductName($product)) ?></a>
        </h3>
        <p class="price"><?= number_format($product['price'], 2, ',', '.') ?> € / <?= htmlspecialchars($product['unit']) ?></p>
        <div class="card-actions">
          <button class="add-btn" data-id="<?= $product['id'] ?>"><?= Lang::get('product.add') ?></button>
          <a href="/product?id=<?= $product['id'] ?>" class="link"><?= Lang::get('product.view') ?></a>
        </div>
      </div>
    </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<style>
.left-panel {
  position: absolute;
  left: 24px;
  top: 24px;
  width: 280px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  padding: 16px;
  z-index: 2;
}

.panel-logo {
  display: flex;
  align-items: baseline;
  gap: 4px;
  margin-bottom: 16px;
}

.panel-separator {
  height: 2px;
  background: var(--color-dark);
  margin-bottom: 16px;
}

.panel-section {
  margin-bottom: 20px;
}

.panel-section-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-dark);
  margin-bottom: 8px;
  text-transform: uppercase;
}

.panel-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.panel-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 0;
  font-size: 13px;
}

.panel-item a {
  color: var(--color-dark);
  text-decoration: none;
}

.panel-item a:hover {
  text-decoration: underline;
}

.panel-item-dash {
  color: var(--color-dark);
  font-size: 13px;
  padding: 4px 0;
  font-weight: 600;
}

.panel-sublist {
  list-style: none;
  padding-left: 24px;
  margin-top: 4px;
}

.panel-sublist li {
  padding: 3px 0;
  font-size: 12px;
  color: var(--color-text-muted);
}

.panel-plus {
  color: var(--color-primary);
  font-weight: 700;
  font-size: 16px;
}

.filters {
  margin: 32px 0;
}

.price-filter {
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
  max-width: 520px;
}

.filter-title {
  font-size: 12px;
  letter-spacing: 0.14em;
  color: var(--color-text-muted);
  margin-bottom: 12px;
  text-transform: uppercase;
}

.price-bubbles {
  display: flex;
  gap: 8px;
  margin-bottom: 12px;
}

.price-bubbles span {
  background: #f3f4f6;
  color: var(--color-dark);
  border-radius: 999px;
  padding: 6px 12px;
  font-size: 13px;
  font-weight: 600;
}

.range-track {
  position: relative;
  height: 24px;
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}

.range-input {
  appearance: none;
  -webkit-appearance: none;
  width: 100%;
  height: 4px;
  background: var(--color-border);
  position: absolute;
  outline: none;
}

.range-input::-webkit-slider-thumb {
  appearance: none;
  -webkit-appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: var(--color-dark);
  cursor: pointer;
}

.range-input::-moz-range-thumb {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: var(--color-dark);
  cursor: pointer;
  border: none;
}

.price-filter .primary {
  background: var(--color-primary);
  color: var(--color-dark);
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 700;
  cursor: pointer;
  font-size: 14px;
}

@media (max-width: 768px) {
  .left-panel {
    position: static;
    width: auto;
    margin: 16px;
  }
  
  .category-title {
    font-size: 24px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Filtro de precio
  const minRange = document.getElementById('price-min');
  const maxRange = document.getElementById('price-max');
  const minLabel = document.getElementById('price-min-label');
  const maxLabel = document.getElementById('price-max-label');
  const applyBtn = document.getElementById('price-apply');
  
  function updateLabels() {
    if (minLabel && minRange) minLabel.textContent = minRange.value + ' €';
    if (maxLabel && maxRange) maxLabel.textContent = maxRange.value + ' €';
  }
  
  if (minRange && maxRange) {
    minRange.addEventListener('input', updateLabels);
    maxRange.addEventListener('input', updateLabels);
    updateLabels();
  }
  
  // Agregar al carrito usando función global de app.js
  document.querySelectorAll('.add-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const productId = e.target.getAttribute('data-id');
      if (typeof addToCart === 'function') {
        await addToCart(productId, 1);
      } else {
        // Fallback si addToCart no está disponible
        try {
          const response = await fetch('/api/cart-add.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
          });
          const data = await response.json();
          if (data.success) {
            showNotification('Producto añadido al carrito', 'success');
            const countEl = document.getElementById('cart-count');
            if (countEl) countEl.textContent = data.count || 0;
          }
        } catch (err) {
          alert('Error al agregar el producto');
        }
      }
    });
  });
});
</script>
