<?php
/**
 * Archivo de ejemplo de configuración de entorno
 * Copiar este archivo a .env.php y configurar tus credenciales
 */

// Google OAuth
putenv('GOOGLE_CLIENT_ID=tu_google_client_id');
putenv('GOOGLE_CLIENT_SECRET=tu_google_client_secret');

// Facebook OAuth
putenv('FACEBOOK_APP_ID=tu_facebook_app_id');
putenv('FACEBOOK_APP_SECRET=tu_facebook_app_secret');

// Apple OAuth
putenv('APPLE_CLIENT_ID=tu_apple_client_id');
putenv('APPLE_TEAM_ID=tu_apple_team_id');
putenv('APPLE_KEY_ID=tu_apple_key_id');

// Stripe
putenv('STRIPE_PUBLIC_KEY=pk_test_tu_public_key');
putenv('STRIPE_SECRET_KEY=sk_test_tu_secret_key');
putenv('STRIPE_WEBHOOK_SECRET=whsec_tu_webhook_secret');

