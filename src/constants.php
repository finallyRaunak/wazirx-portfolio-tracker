<?php

// Your website your where the app is hosted. pint it till the dir where src dir in kept.
if (!\defined('BASE_URL')) {
    \define('BASE_URL', 'http://example.com/tools/wrx');
}

// Disable it in a producton envinonment.
if (!\defined('APP_DEBUG')) {
    \define('APP_DEBUG', false);
}

// Trading pair caching TTL In minutes
if (!\defined('TRADING_PAIR_TTL')) {
    \define('TRADING_PAIR_TTL', 120);
}

// Order caching TTL In minutes
if (!\defined('MY_ORDER_TTL')) {
    \define('MY_ORDER_TTL', 60);
}

// Tickers/Current Market Rate caching TTL In minutes
if (!\defined('TICKERS_TTL')) {
    \define('TICKERS_TTL', 5);
}

// Ticker/Current Market Rate (for a single pares) caching TTL In minutes
if (!\defined('TICKER_TTL')) {
    \define('TICKER_TTL', 10);
}

// Funds/Wallet info caching TTL In minutes
if (!\defined('MY_WALLET_TTL')) {
    \define('MY_WALLET_TTL', 30);
}
