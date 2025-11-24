<?php
/**
 * Landing Page según diseño de Figma
 * Header amarillo con productos de pescado frescos
 */
$pageTitle = 'Inicio';
$dbConnected = false;
$tablesExist = false;

try {
    $db = Database::getInstance();
    $dbConnected = $db->isConnected();
    if ($dbConnected) {
        $tablesExist = Product::tablesExist();
    }
    $featuredProducts = $dbConnected && $tablesExist ? Product::getFeatured() : [];
    if (!is_array($featuredProducts)) {
        $featuredProducts = [];
    }
} catch (Exception $e) {
    error_log("Error obteniendo productos destacados: " . $e->getMessage());
    $featuredProducts = [];
}
?>

<section class="category-hero landing-hero" style="background-image:url('<?= APP_URL ?>/assets/images/Landing Page1.png')">
  <div class="category-overlay">
    <div class="container">
      <h1 class="category-title">PESCADO FRESCO</h1>
      <p class="category-subtitle">Productos frescos de calidad superior</p>
    </div>
  </div>
</section>

<section class="how-works" style="background-image:url('<?= APP_URL ?>/assets/images/Landing Page2.png')">
  <div class="container">
    <h2 class="how-title"><?= Lang::get('home.how_works') ?></h2>
    <div class="how-grid">
      <div class="how-card">
        <div class="how-icon">WWW</div>
        <p><?= Lang::get('home.how_1') ?></p>
      </div>
      <div class="how-card">
        <div class="how-icon">⏱️</div>
        <p><?= Lang::get('home.how_2') ?></p>
      </div>
      <div class="how-card">
        <div class="how-icon">🚗</div>
        <p><?= Lang::get('home.how_3') ?></p>
      </div>
    </div>
  </div>
</section>

<section class="section container">
  <h2 class="section-title"><?= Lang::get('home.featured') ?></h2>
  <div id="product-grid" class="grid">
    <?php if (!$dbConnected || !$tablesExist): ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--color-text-muted);">
        <p style="font-size: 18px; margin-bottom: 16px;"><?= Lang::get('db.not_configured') ?></p>
        <p style="font-size: 14px; margin-bottom: 20px;"><?= CURRENT_LANG === 'es' ? 'Es necesario instalar la base de datos para que el sitio funcione correctamente.' : 'Cal instal·lar la base de dades perquè el lloc funcioni correctament.' ?></p>
        <a href="<?= APP_URL ?>/install/install.php" style="display: inline-block; padding: 12px 24px; background: var(--color-primary); color: var(--color-dark); text-decoration: none; border-radius: 8px; font-weight: 600;"><?= Lang::get('db.install') ?></a>
      </div>
    <?php elseif (empty($featuredProducts)): ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--color-text-muted);">
        <p style="font-size: 18px; margin-bottom: 16px;">No hay productos destacados disponibles</p>
        <p style="font-size: 14px;">Próximamente agregaremos productos nuevos.</p>
      </div>
    <?php else: ?>
      <?php foreach ($featuredProducts as $product): ?>
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
            <button class="add-btn" data-id="<?= $product['id'] ?>"><?= Lang::get('product.add_to_cart') ?></button>
            <a href="/product?id=<?= $product['id'] ?>" class="secondary link"><?= Lang::get('product.view') ?></a>
    </div>
  </div>
      </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Agregar al carrito usando función global de app.js
  document.querySelectorAll('.add-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const productId = e.target.getAttribute('data-id');
      if (typeof addToCart === 'function') {
        await addToCart(productId, 1);
      } else {
        // Fallback
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
