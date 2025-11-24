<?php
/**
 * Gestión de OAuth (Google, Facebook, Apple)
 */
class OAuth {
    public static function getGoogleAuthUrl() {
        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    public static function handleGoogleCallback($code) {
        // Intercambiar código por token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $data = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            return false;
        }

        // Obtener información del usuario
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenData['access_token']]);
        $userResponse = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($userResponse, true);
        if (!isset($userData['id'])) {
            return false;
        }

        return [
            'provider' => 'google',
            'oauth_id' => $userData['id'],
            'email' => $userData['email'] ?? '',
            'name' => $userData['name'] ?? '',
            'avatar' => $userData['picture'] ?? null
        ];
    }

    public static function getFacebookAuthUrl() {
        $params = [
            'client_id' => FACEBOOK_APP_ID,
            'redirect_uri' => FACEBOOK_REDIRECT_URI,
            'scope' => 'email,public_profile',
            'response_type' => 'code'
        ];
        return 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query($params);
    }

    public static function handleFacebookCallback($code) {
        // Intercambiar código por token
        $tokenUrl = 'https://graph.facebook.com/v18.0/oauth/access_token';
        $data = [
            'client_id' => FACEBOOK_APP_ID,
            'client_secret' => FACEBOOK_APP_SECRET,
            'redirect_uri' => FACEBOOK_REDIRECT_URI,
            'code' => $code
        ];

        $ch = curl_init($tokenUrl . '?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($response, true);
        if (!isset($tokenData['access_token'])) {
            return false;
        }

        // Obtener información del usuario
        $userInfoUrl = 'https://graph.facebook.com/v18.0/me?fields=id,name,email,picture';
        $ch = curl_init($userInfoUrl . '&access_token=' . $tokenData['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userResponse = curl_exec($ch);
        curl_close($ch);

        $userData = json_decode($userResponse, true);
        if (!isset($userData['id'])) {
            return false;
        }

        return [
            'provider' => 'facebook',
            'oauth_id' => $userData['id'],
            'email' => $userData['email'] ?? '',
            'name' => $userData['name'] ?? '',
            'avatar' => $userData['picture']['data']['url'] ?? null
        ];
    }

    public static function getAppleAuthUrl() {
        // Apple Sign In requiere configuración más compleja
        // Por ahora, retornar URL base
        return '#';
    }
}


