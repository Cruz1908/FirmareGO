<?php
/**
 * Página de inicio de sesión
 */
$pageTitle = 'Iniciar sesión';
$error = $_GET['error'] ?? null;
?>

<div class="container" style="padding: 60px 20px; max-width: 480px;">
  <h1 class="page-title"><?= Lang::get('login.title') ?></h1>
  
  <?php if ($error): ?>
    <div class="alert alert-error" style="background: #fee; border: 1px solid #fcc; padding: 12px; border-radius: 8px; margin-bottom: 20px; color: #c33;">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>
  
  <form method="POST" action="/api/login.php" class="auth-form">
    <div class="form-group">
        <label class="label"><?= Lang::get('checkout.email') ?></label>
      <input type="email" name="email" class="input" required autocomplete="email" />
      </div>
    
    <div class="form-group">
      <label class="label"><?= Lang::get('login.password') ?></label>
      <input type="password" name="password" class="input" required autocomplete="current-password" />
    </div>
    
    <button type="submit" class="primary" style="width: 100%; margin-top: 20px;"><?= Lang::get('login.enter') ?></button>
    
    <div style="text-align: center; margin-top: 20px;">
      <p style="color: var(--color-text-muted);"><?= Lang::get('login.no_account') ?> <a href="/register"><?= Lang::get('login.register') ?></a></p>
    </div>
    
    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--color-border);">
      <p style="text-align: center; color: var(--color-text-muted); margin-bottom: 16px;"><?= Lang::get('login.with') ?></p>
    <div class="oauth-buttons">
        <a href="/api/oauth/google.php" class="oauth-btn google">
          <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
          Google
        </a>
        <a href="/api/oauth/facebook.php" class="oauth-btn facebook">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          Facebook
        </a>
        <a href="#" class="oauth-btn apple" onclick="alert('Apple Sign In próximamente'); return false;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>
          Apple
        </a>
      </div>
    </div>
  </form>
</div>

<style>
.auth-form {
  background: #fff;
  border: 1px solid var(--color-border);
  border-radius: 12px;
  padding: 32px;
}

.form-group {
  margin-bottom: 20px;
  }

.label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: var(--color-dark);
}

.input {
  width: 100%;
  padding: 12px;
  border: 1px solid var(--color-border);
  border-radius: 8px;
  font-size: 16px;
  background: #fff;
    }

.input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(255, 210, 0, 0.1);
}

.page-title {
  font-size: 32px;
  font-weight: 800;
  margin-bottom: 32px;
  text-align: center;
}
</style>
