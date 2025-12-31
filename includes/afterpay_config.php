<?php
// Afterpay Configuration

// Environment Settings
define('AFTERPAY_SANDBOX', true); // Set to false for production
define('AFTERPAY_ENVIRONMENT', AFTERPAY_SANDBOX ? 'sandbox' : 'production');

// API Credentials
// REPLACE THESE WITH YOUR ACTUAL AFTERPAY CREDENTIALS
define('AFTERPAY_MERCHANT_ID', 'LK2KJD43KFSF2');
define('AFTERPAY_SECRET_KEY', 'Ez0Agq_Niw9bi4DzRna92WhTvesJYWaqKzSvAT70XQ1wO5gm079jp7cNcl5Hoi_dGQKWjYrOg8bFYNG4');

// API Endpoints
$afterpay_endpoints = [
    'sandbox' => 'https://api-sandbox.afterpay.com/v2',
    'production' => 'https://api.afterpay.com/v2'
];
define('AFTERPAY_API_URL', $afterpay_endpoints[AFTERPAY_ENVIRONMENT]);

// Afterpay JS SDK
$afterpay_js_sdk = [
    'sandbox' => 'https://portal.sandbox.afterpay.com/afterpay.js',
    'production' => 'https://portal.afterpay.com/afterpay.js'
];
define('AFTERPAY_JS_SDK', $afterpay_js_sdk[AFTERPAY_ENVIRONMENT]);

// URLs
define('AFTERPAY_RETURN_URL', 'http://localhost/bookingsytem/booking/afterpay_success.php');
define('AFTERPAY_CANCEL_URL', 'http://localhost/bookingsytem/booking/payment_cancel.php');

// Currency
define('AFTERPAY_CURRENCY', 'USD');

// Minimum and Maximum Order Values (Afterpay requirements)
define('AFTERPAY_MIN_AMOUNT', 1.00);
define('AFTERPAY_MAX_AMOUNT', 2000.00);
?>
