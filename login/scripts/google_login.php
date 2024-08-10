<?php
require_once '../../vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://yourdomain.com/google_login.php');
$client->addScope('email');
$client->addScope('profile');

$service = new Google_Service_Oauth2($client);

if (!isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $client->setAccessToken($_SESSION['access_token']);
    $user_info = $service->userinfo->get();
    
    // Maneja la autenticación del usuario (por ejemplo, buscar o crear el usuario en la base de datos)
    
    header('Location: dashboard.php');
}

