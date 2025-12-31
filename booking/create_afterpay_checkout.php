<?php
require_once '../includes/db.php';
require_once '../includes/afterpay_config.php';

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

$order_id = isset($input['order_id']) ? (int)$input['order_id'] : 0;
$amount = isset($input['amount']) ? (float)$input['amount'] : 0;
$currency = isset($input['currency']) ? $input['currency'] : AFTERPAY_CURRENCY;

if ($order_id === 0 || $amount === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid order or amount']);
    exit;
}

// Fetch Order Details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'error' => 'Order not found']);
    exit;
}

// Prepare Afterpay checkout request
$checkout_data = [
    'amount' => [
        'amount' => number_format($amount, 2, '.', ''),
        'currency' => $currency
    ],
    'consumer' => [
        'email' => $order['customer_email'],
        'givenNames' => explode(' ', $order['customer_name'])[0],
        'surname' => isset(explode(' ', $order['customer_name'])[1]) ? explode(' ', $order['customer_name'])[1] : 'Customer',
        'phoneNumber' => $order['customer_phone']
    ],
    'merchant' => [
        'redirectConfirmUrl' => AFTERPAY_RETURN_URL . '?order_id=' . $order_id,
        'redirectCancelUrl' => AFTERPAY_CANCEL_URL . '?order_id=' . $order_id
    ],
    'merchantReference' => $order['order_code']
];

// Create Afterpay checkout
$auth = base64_encode(AFTERPAY_MERCHANT_ID . ':' . AFTERPAY_SECRET_KEY);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, AFTERPAY_API_URL . '/checkouts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . $auth,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($checkout_data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 201 || $http_code === 200) {
    $afterpay_response = json_decode($response, true);
    
    if (isset($afterpay_response['token']) && isset($afterpay_response['redirectCheckoutUrl'])) {
        echo json_encode([
            'success' => true,
            'token' => $afterpay_response['token'],
            'redirectCheckoutUrl' => $afterpay_response['redirectCheckoutUrl']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid Afterpay response',
            'details' => $afterpay_response
        ]);
    }
} else {
    error_log("Afterpay checkout creation failed: HTTP $http_code - $response");
    file_put_contents('debug_afterpay.txt', "Time: " . date('Y-m-d H:i:s') . "\nHTTP Code: $http_code\nResponse: $response\nRequest Data: " . json_encode($checkout_data) . "\nAuth Header: Basic " . $auth . "\n----------------\n", FILE_APPEND);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to create Afterpay checkout',
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ]);
}
?>
