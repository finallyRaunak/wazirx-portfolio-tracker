<?php
session_start();
require_once 'vendor/autoload.php';

if(!APP_DEBUG){
    error_reporting(0);
}
else{
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

if (!empty(filter_input(INPUT_POST, 'consent', FILTER_SANITIZE_STRING))) {
    if ($_SESSION['csrf_token'] !== filter_input(INPUT_POST, 'csrf_token', FILTER_SANITIZE_STRING)) {
        
        // CSRF miss-match so redirecting to home page
        header('Location: '. getSiteURL('/'));
        die();
    }
    $tradingPair = !empty(filter_input(INPUT_POST, 'trading_pair', FILTER_DEFAULT , FILTER_REQUIRE_ARRAY)) ? $_POST['trading_pair'] : ['inr'];
    $apiKey = $_SESSION['api_key'] = filter_input(INPUT_POST, 'api_key', FILTER_SANITIZE_STRING);
    $apiSecret = $_SESSION['api_secret'] = filter_input(INPUT_POST, 'api_secret', FILTER_SANITIZE_STRING);
    
    $obj = new \BalanceSheet\BalanceSheet();
    $obj->listOrders($apiKey, $apiSecret, $tradingPair);
    die();
}

$obj = new \BalanceSheet\BalanceSheet();
$obj->index();
