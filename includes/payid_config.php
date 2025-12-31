<?php
// PayID Configuration

// Business Details for PayID Display
define('PAYID_BUSINESS_NAME', 'Dr. Spin'); // Update with your business name
define('PAYID_MOBILE_NUMBER', '0431500738'); // Update with your mobile number
define('PAYID_EMAIL', 'payments@drspin.com'); // Optional: Update with your email

// PayID Barcode Image
define('PAYID_BARCODE_IMAGE', '/bookingsytem/assets/images/payid_barcode_placeholder.png');

// Payment Instructions
define('PAYID_INSTRUCTIONS', 'Scan the barcode with your banking app to complete the payment. Once payment is sent, click "I\'ve Completed Payment" below.');

// Transaction ID Prefix
define('PAYID_TRANSACTION_PREFIX', 'PAYID-');
?>
