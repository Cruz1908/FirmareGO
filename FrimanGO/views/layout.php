<?php
/**
 * Plantilla base del sitio
 */
?><!doctype html>
<html lang="<?= CURRENT_LANG ?>">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?> - <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= APP_URL ?>/styles.css" />
</head>
<body class="theme-friman">
  <?php require __DIR__ . '/partials/navbar.php'; ?>
  <main>
    <?php require $contentView; ?>
  </main>
  <script src="<?= APP_URL ?>/app.js"></script>
  <script type="module">
    // Gestión de sesión del usuario
      const loginLink = document.getElementById('login-link');
      const userName = document.getElementById('user-name');
      const logoutBtn = document.getElementById('logout-btn');
    
    <?php if (Auth::isLoggedIn()): ?>
      const user = <?= json_encode(Auth::getCurrentUser()) ?>;
      if (loginLink) loginLink.style.display = 'none';
      if (userName) {
        userName.textContent = user.name;
        userName.style.display = 'inline-block';
      }
      if (logoutBtn) {
        logoutBtn.style.display = 'inline-block';
        logoutBtn.onclick = () => {
          fetch('/api/logout.php', { method: 'POST' })
            .then(() => location.reload());
        };
      }
    <?php else: ?>
      if (loginLink) loginLink.style.display = 'inline-block';
      if (userName) userName.style.display = 'none';
      if (logoutBtn) logoutBtn.style.display = 'none';
    <?php endif; ?>
    
    // Actualizar contador del carrito
    function updateCartCount() {
      fetch('/api/cart-count.php')
        .then(r => r.json())
        .then(data => {
          const countEl = document.getElementById('cart-count');
          if (countEl) countEl.textContent = data.count || 0;
        });
    }
    updateCartCount();
    setInterval(updateCartCount, 2000);
    
    // Cambiar idioma
    document.querySelectorAll('.lang-btn').forEach(btn => {
      btn.addEventListener('click', async () => {
        const lang = btn.getAttribute('data-lang');
        try {
          const formData = new FormData();
          formData.append('lang', lang);
          
          const response = await fetch('/api/lang-change.php', {
            method: 'POST',
            body: formData
          });
          
          const data = await response.json();
          if (data.success) {
            // Recargar la página para aplicar el nuevo idioma
            window.location.reload();
          }
        } catch (err) {
          console.error('Error cambiando idioma:', err);
        }
      });
    });
  </script>
</body>
</html>
