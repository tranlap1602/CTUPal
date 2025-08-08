<?php
/**
 * Google API Configuration
 * Cấu hình cho Google Calendar API
 */

// Google OAuth2 credentials
// Bạn cần tạo credentials tại: https://console.developers.google.com/
define('GOOGLE_CLIENT_ID', 'your-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your-client-secret');
define('GOOGLE_REDIRECT_URI', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/google-auth.php');

// Google Calendar API scopes
define('GOOGLE_SCOPES', ['https://www.googleapis.com/auth/calendar']);

// Application information
define('GOOGLE_APPLICATION_NAME', 'CTUPal Calendar Integration');

/**
 * Khởi tạo Google Client
 */
function getGoogleClient() {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $client = new Google\Client();
    $client->setApplicationName(GOOGLE_APPLICATION_NAME);
    $client->setClientId(GOOGLE_CLIENT_ID);
    $client->setClientSecret(GOOGLE_CLIENT_SECRET);
    $client->setRedirectUri(GOOGLE_REDIRECT_URI);
    $client->setScopes(GOOGLE_SCOPES);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');
    
    return $client;
}

/**
 * Kiểm tra và refresh access token nếu cần
 */
function refreshTokenIfNeeded($client, $accessToken) {
    $client->setAccessToken($accessToken);
    
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $newAccessToken = $client->getAccessToken();
            
            // Lưu access token mới vào session
            $_SESSION['google_access_token'] = $newAccessToken;
            return $newAccessToken;
        } else {
            // Không có refresh token, cần đăng nhập lại
            return false;
        }
    }
    
    return $accessToken;
}

/**
 * Khởi tạo Google Calendar Service
 */
function getCalendarService() {
    if (!isset($_SESSION['google_access_token'])) {
        return false;
    }
    
    $client = getGoogleClient();
    $accessToken = refreshTokenIfNeeded($client, $_SESSION['google_access_token']);
    
    if (!$accessToken) {
        return false;
    }
    
    $client->setAccessToken($accessToken);
    return new Google\Service\Calendar($client);
}