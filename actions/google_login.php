<?php
// google_login.php - Initiates Google OAuth 2.0 redirect
require_once 'includes/supabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$clientId = getenv('GOOGLE_CLIENT_ID');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$hostName = $_SERVER['HTTP_HOST'];
$projectFolder = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$redirectUri = "{$protocol}://{$hostName}{$projectFolder}/google_callback.php";

$params = [
    'client_id'     => $clientId,
    'redirect_uri'  => $redirectUri,
    'response_type' => 'code',
    'scope'         => 'email profile',
    'access_type'   => 'online'
];

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params));
exit();
?>