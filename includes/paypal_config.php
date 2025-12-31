<?php
// PayPal Configuration
define('PAYPAL_SANDBOX', true); // Set to false for production

// PayPal Sandbox Credentials
define('PAYPAL_SANDBOX_EMAIL', 'sb-nnphu48022499@business.example.com');
define('PAYPAL_MERCHANT_ID', 'LS3Q25S65NUA2');
define('PAYPAL_CLIENT_ID', 'AdkD8ZRu9WM5E7ez_sB19khmywU72JaQFtxN7UMJMEzNsp3V9gKHXBTJaZU2kr-Cnbq40KzQrkSSIbI6');
define('PAYPAL_SECRET', 'EKzSvAT70XQjpzDesJY1wz0Agq_Niw9bi4m079WaqO5gROg8b7cNna92WhTvQKWjYrcl5Hoi_dGFYNG4'); 

// PayPal API Endpoints
define('PAYPAL_API_URL', PAYPAL_SANDBOX ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com');

// URLs
// Adjust these if your local setup is different
define('PAYPAL_RETURN_URL', 'http://localhost/bookingsytem/booking/payment_success.php');
define('PAYPAL_CANCEL_URL', 'http://localhost/bookingsytem/booking/payment_cancel.php');
define('PAYPAL_CURRENCY', 'USD');
?>
