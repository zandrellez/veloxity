<?php
// actions/facebook_login.php - Initiates Facebook OAuth 2.0 redirect
require_once '../includes/supabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$appId = getenv('FACEBOOK_APP_ID');

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$hostName = $_SERVER['HTTP_HOST'];
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$redirectUri = "{$protocol}://{$hostName}{$scriptDir}/facebook_callback.php";

$params = [
    'client_id'     => $appId,
    'redirect_uri'  => $redirectUri,
    'scope'         => 'email,public_profile',
    'response_type' => 'code',
    'auth_type'     => 'rerequest'
];

header('Location: https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query($params));
exit();
?>