<?php
/**
 * Página del carrito de compras
 */
$pageTitle = 'Carrito';
$cart = Cart::get();
$total = Cart::getTotal();
?>

<div class="container" style="padding: 40px 20px;">
  <h1 class="page-title"><?= Lang::get('cart.title') ?></h1>
  
  <?php if (empty($cart)): ?>
    <div class="cart-empty">
      <div class="empty-icon">🛒</div>
      <h2><?= Lang::get('cart.empty') ?></h2>
      <p><?= Lang::get('cart.empty_desc') ?></p>
      <a href="/category" class="primary" style="margin-top: 20px; display: inline-block;"><?= Lang::get('cart.explore') ?></a>
    </div>
  <?php else: ?>
    <div class="cart-layout">
      <div class="cart-items-section">
        <table class="cart-table">
          <thead>
            <tr>
              <th><?= Lang::get('cart.product') ?></th>
              <th><?= Lang::get('cart.price') ?></th>
              <th><?= Lang::get('cart.quantity') ?></th>
              <th><?= Lang::get('cart.total') ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart as $item): ?>
            <tr data-id="<?= $item['id'] ?>">
              <td>
                <div class="cart-item-info">
                  <strong><?= htmlspecialchars(Lang::getProductName($item)) ?></strong>
                  <span class="cart-item-unit">/ <?= htmlspecialchars($item['unit']) ?></span>
                </div>
              </td>
              <td class="cart-price"><?= number_format($item['price'], 2, ',', '.') ?> €</td>
              <td>
                <div class="qty-controls">
                  <button class="qty-btn" data-id="<?= $item['id'] ?>" data-delta="-1">−</button>
                  <span class="qty-value"><?= $item['quantity'] ?></span>
                  <button class="qty-btn" data-id="<?= $item['id'] ?>" data-delta="1">+</button>
                </div>
              </td>
              <td class="cart-total-item"><?= number_format($item['price'] * $item['quantity'], 2, ',', '.') ?> €</td>
              <td>
                <button class="remove-btn" data-id="<?= $item['id'] ?>"><?= Lang::get('cart.remove') ?></button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
      </div>
      
      <div class="cart-summary">
        <div class="summary-card">
          <h3><?= Lang::get('cart.summary') ?></h3>
          <div class="summary-row">
            <span><?= Lang::get('cart.subtotal') ?></span>
            <span><?= number_format($total, 2, ',', '.') ?> €</span>
  </div>
          <div class="summary-row">
            <span><?= Lang::get('cart.shipping') ?></span>
            <span><?= Lang::get('cart.shipping_free') ?></span>
</div>
          <div class="summary-total">
            <span><?= Lang::get('cart.total') ?></span>
            <strong><?= number_format($total, 2, ',', '.') ?> €</strong>
          </div>
          <a href="/checkout" class="checkout-btn"><?= Lang::get('cart.go_to_checkout') ?></a>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<style>
.page-title {
  font-size: 32px;
  font-weight: 800;
  margin-bottom: 32px;
}

.cart-empty {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
}

.cart-layout {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 32px;
  align-items: start;
}

.cart-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

.cart-table thead {
  background: #f9fafb;
}

.cart-table th {
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: var(--color-dark);
  border-bottom: 1px solid var(--color-border);
}

.cart-table td {
  padding: 16px;
  border-bottom: 1px solid var(--color-border);
}

.cart-item-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.cart-item-unit {
  font-size: 12px;
  color: var(--color-text-muted);
}

.cart-price, .cart-total-item {
  font-weight: 600;
  color: var(--color-dark);
}

.qty-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.qty-btn {
  width: 32px;
  height: 32px;
  border: 1px solid var(--color-border);
  background: #fff;
  border-radius: 6px;
  cursor: pointer;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.qty-btn:hover {
  background: #f9fafb;
}

.qty-value {
  min-width: 40px;
  text-align: center;
  font-weight: 600;
}

.remove-btn {
  background: var(--color-danger);
  color: #fff;
  border: 0;
  border-radius: 6px;
  padding: 6px 12px;
  cursor: pointer;
  font-size: 12px;
}

.remove-btn:hover {
  background: #dc2626;
}

.summary-card {
  background: #fff;
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 24px;
  position: sticky;
  top: 100px;
}

.summary-card h3 {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 20px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px solid var(--color-border);
  color: var(--color-text-muted);
}

.summary-total {
  display: flex;
  justify-content: space-between;
  padding: 16px 0;
  margin-top: 8px;
  font-size: 20px;
  font-weight: 700;
}

.checkout-btn {
  display: block;
  width: 100%;
  background: var(--color-primary);
  color: var(--color-dark);
  border: 0;
  border-radius: 8px;
  padding: 14px;
  text-align: center;
  text-decoration: none;
  font-weight: 700;
  font-size: 16px;
  margin-top: 20px;
  cursor: pointer;
}

.checkout-btn:hover {
  background: var(--color-primary-dark);
}

@media (max-width: 900px) {
  .cart-layout {
    grid-template-columns: 1fr;
  }
  
  .summary-card {
    position: static;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Cambiar cantidad
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const productId = btn.getAttribute('data-id');
      const delta = parseInt(btn.getAttribute('data-delta'));
      const qtyEl = btn.closest('tr').querySelector('.qty-value');
      const currentQty = parseInt(qtyEl.textContent);
      const newQty = Math.max(0, currentQty + delta);
      
      try {
        const response = await fetch('/api/cart-update.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ product_id: productId, quantity: newQty })
        });
        
        if (response.ok) {
          location.reload();
        }
      } catch (err) {
        alert('Error al actualizar la cantidad');
      }
    });
  });
  
  // Eliminar producto
  document.querySelectorAll('.remove-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const productId = btn.getAttribute('data-id');
      
      if (!confirm('¿Eliminar este producto del carrito?')) return;
      
      try {
        const response = await fetch('/api/cart-remove.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ product_id: productId })
        });
        
        if (response.ok) {
          location.reload();
        }
      } catch (err) {
        alert('Error al eliminar el producto');
      }
    });
  });
});
</script>
