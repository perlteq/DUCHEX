
<?php
require_once 'vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setClientId('YOUR_CLIENT_ID');
$client->setClientSecret('YOUR_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/google-signin-callback.php');
$client->addScope("email");
$client->addScope("profile");

$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

?>