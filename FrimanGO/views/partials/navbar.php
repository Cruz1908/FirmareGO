<?php
/**
 * Barra de navegación según diseño de Figma
 * Header amarillo (#FFD200) con logo, búsqueda, login y carrito
 */
?>
<header class="navbar navbar-friman">
  <button class="icon menu-toggle" aria-label="Menú">☰</button>
  <a href="/" class="brand">
    <span class="brand-text">
      <span class="friman">friman</span>
      <span class="go">GO</span>
    </span>
    <span class="tagline">CLICK & COLLECT</span>
  </a>
  <div class="searchbar">
    <input type="text" placeholder="<?= Lang::get('nav.search') ?>" aria-label="<?= Lang::get('nav.search') ?>" id="search-input" />
    <button class="search-btn" aria-label="<?= Lang::get('nav.search') ?>">🔍</button>
  </div>
  <nav class="nav-links">
    <a href="/"><?= Lang::get('nav.home') ?></a>
    <a href="/category"><?= Lang::get('nav.products') ?></a>
  </nav>
  <div class="account navbar-icons">
    <!-- Selector de idioma -->
    <div class="lang-selector" style="display: inline-flex; align-items: center; gap: 8px; margin-right: 12px;">
      <button class="lang-btn <?= CURRENT_LANG === 'es' ? 'active' : '' ?>" data-lang="es" style="padding: 4px 8px; border: 1px solid var(--color-border); background: <?= CURRENT_LANG === 'es' ? 'var(--color-primary)' : '#fff' ?>; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">ES</button>
      <button class="lang-btn <?= CURRENT_LANG === 'ca' ? 'active' : '' ?>" data-lang="ca" style="padding: 4px 8px; border: 1px solid var(--color-border); background: <?= CURRENT_LANG === 'ca' ? 'var(--color-primary)' : '#fff' ?>; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">CA</button>
    </div>
    <a href="/login" id="login-link"><?= Lang::get('nav.login') ?></a>
    <a href="#" class="help-link mobile-hide"><?= Lang::get('nav.help') ?></a>
    <span id="user-name" class="mobile-hide"></span>
    <button id="logout-btn" class="secondary mobile-hide" style="display:none"><?= Lang::get('nav.logout') ?></button>
  </div>
  <button id="cart-button" class="cart-button" aria-label="<?= Lang::get('nav.cart') ?>">
    <span class="cart-icon">🛒</span>
    <span class="mobile-hide"><?= Lang::get('nav.cart') ?></span>
    <span id="cart-count" class="cart-badge">0</span>
  </button>
</header>

<!-- Menú lateral (desde diseño MENU.png) -->
<aside id="side-menu" class="side-menu hidden">
  <div class="menu-header">
    <button class="menu-close" aria-label="Cerrar menú">×</button>
  </div>
  <div class="menu-content">
    <div class="menu-logo">
      <span class="friman">friman</span>
      <span class="go">GO</span>
      <div class="tagline">CLICK & COLLECT</div>
    </div>
    <div class="menu-separator"></div>
    
    <?php
    try {
        $categories = Product::getCategories();
    } catch (Exception $e) {
        $categories = [];
        error_log("Error obteniendo categorías: " . $e->getMessage());
    }
    
    if (empty($categories)) {
        // Si no hay categorías, mostrar un mensaje o menú vacío
        echo '<div style="padding: 20px; color: #6b7280; font-size: 14px;">No hay categorías disponibles</div>';
    } else {
        $currentType = null;
        foreach ($categories as $cat):
      if ($cat['parent_id'] === null):
        if ($currentType !== null) echo '</ul>';
        $currentType = $cat['type'];
    ?>
    <div class="menu-section">
      <h4 class="menu-section-title"><?= htmlspecialchars(Lang::getCategoryName($cat)) ?></h4>
      <ul class="menu-list">
      <?php
      else:
        if ($cat['parent_id'] == 1 && strtolower($cat['code']) == 'verduras'): // VERDURAS es especial
      ?>
        <li class="menu-item has-children">
          <span class="menu-dash">–</span>
          <span class="menu-item-text"><?= htmlspecialchars(Lang::getCategoryName($cat)) ?></span>
          <ul class="menu-sublist">
            <li><?= CURRENT_LANG === 'es' ? 'FRUTAS CONGELADAS' : 'FRUITE CONGELADES' ?></li>
            <li><?= CURRENT_LANG === 'es' ? 'SOLO REGENERAR' : 'NOMES REGENERAR' ?></li>
            <li><?= CURRENT_LANG === 'es' ? 'PREPARADOS DE PROTEÍNA VEGETAL' : 'PREPARATS DE PROTEINA VEGETAL' ?></li>
            <li><?= CURRENT_LANG === 'es' ? 'VERDURAS AL NATURAL' : 'VERDURES AL NATURAL' ?></li>
            <li><?= CURRENT_LANG === 'es' ? 'VERDURAS PREPARADAS' : 'VERDURES PREPARADES' ?></li>
          </ul>
        </li>
      <?php else: ?>
        <li class="menu-item">
          <span class="menu-plus">+</span>
          <a href="/category?cat=<?= urlencode($cat['code']) ?>" class="menu-item-link"><?= htmlspecialchars(Lang::getCategoryName($cat)) ?></a>
        </li>
      <?php
        endif;
      endif;
    endforeach;
    if ($currentType !== null) echo '</ul></div>';
    } // Fin del if que verifica si hay categorías
    ?>
  </div>
</aside>

<!-- El JavaScript del navbar se maneja en app.js -->
