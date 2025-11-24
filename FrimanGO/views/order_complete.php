<?php
/**
 * Página de confirmación de orden
 */
$pageTitle = 'Comanda completada';
?>

<div class="container" style="padding: 60px 20px; text-align: center;">
  <div class="success-icon">✓</div>
  <h1 class="page-title">Comanda completada!</h1>
  <p style="font-size: 18px; color: var(--color-text-muted); margin-bottom: 32px;">
    Gràcies per la teva comanda. Rebràs un correu electrònic de confirmació aviat.
  </p>
  <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
    <a href="/" class="primary">Tornar a l'inici</a>
    <a href="/category" class="secondary">Seguir comprant</a>
  </div>
</div>

<style>
.success-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: var(--color-primary);
  color: var(--color-dark);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
  font-weight: 700;
  margin: 0 auto 24px;
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  margin-bottom: 16px;
}
</style>
