<?php
/**
 * Página de producto individual
 */
$productId = intval($_GET['id'] ?? 0);
$product = $productId > 0 ? Product::getById($productId) : null;

if (!$product) {
    header('Location: /views/404.php');
    exit;
}

$pageTitle = Lang::getProductName($product);
?>

<div class="container" style="padding: 40px 20px;">
  <div class="product-grid">
    <div class="product-gallery">
      <?php if ($product['image']): ?>
        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="main-img" />
      <?php else: ?>
        <div class="placeholder-image" style="height: 400px;">Imagen del producto</div>
      <?php endif; ?>
    </div>
    
    <div class="product-info">
      <h1 class="product-title"><?= htmlspecialchars(Lang::getProductName($product)) ?></h1>
      
      <div class="prod-meta">
        <span class="prod-price"><?= number_format($product['price'], 2, ',', '.') ?> €</span>
        <span>/ <?= htmlspecialchars($product['unit']) ?></span>
      </div>
      
      <?php if ($product['description']): ?>
        <div class="prod-description">
          <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        </div>
      <?php endif; ?>
      
      <div class="prod-actions">
        <button class="add-btn" data-id="<?= $product['id'] ?>" style="max-width: 300px;"><?= Lang::get('product.add_to_cart') ?></button>
      </div>
      
      <div class="prod-specs">
        <div class="spec-row">
          <span><?= Lang::get('product.category') ?></span>
          <span><?= htmlspecialchars(ucfirst($product['category'])) ?></span>
        </div>
        <?php if ($product['subcategory']): ?>
        <div class="spec-row">
          <span><?= Lang::get('product.subcategory') ?></span>
          <span><?= htmlspecialchars($product['subcategory']) ?></span>
        </div>
        <?php endif; ?>
        <div class="spec-row">
          <span><?= Lang::get('product.stock') ?></span>
          <span><?= $product['stock'] ?> <?= Lang::get('product.units') ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.product-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
  align-items: start;
}

.product-gallery {
  background: #fff;
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 16px;
}

.main-img {
  width: 100%;
  height: auto;
  border-radius: 8px;
  display: block;
}

.product-info {
  background: #fff;
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 24px;
}

.product-title {
  font-size: 32px;
  font-weight: 800;
  margin-bottom: 16px;
  color: var(--color-dark);
}

.prod-meta {
  display: flex;
  gap: 12px;
  align-items: baseline;
  margin-bottom: 24px;
}

.prod-price {
  color: var(--color-danger);
  font-weight: 700;
  font-size: 28px;
}

.prod-description {
  margin-bottom: 24px;
  color: var(--color-text-muted);
  line-height: 1.6;
}

.prod-actions {
  margin-bottom: 32px;
}

.prod-specs {
  border-top: 1px solid var(--color-border);
  padding-top: 20px;
}

.spec-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
}

.spec-row:last-child {
  border-bottom: none;
}

.spec-row span:first-child {
  color: var(--color-text-muted);
  font-weight: 500;
}

.spec-row span:last-child {
  color: var(--color-dark);
  font-weight: 600;
}

@media (max-width: 860px) {
  .product-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const addBtn = document.querySelector('.add-btn');
  if (addBtn) {
    addBtn.addEventListener('click', async () => {
      const productId = addBtn.getAttribute('data-id');
      if (typeof addToCart === 'function') {
        await addToCart(productId, 1);
      } else {
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
  }
});
</script>
