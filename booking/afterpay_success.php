<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/afterpay_config.php';

// Handle Afterpay return
$order_token = isset($_GET['orderToken']) ? $_GET['orderToken'] : '';
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (empty($order_token) || $order_id === 0) {
    redirect('payment_cancel.php?order_id=' . $order_id);
}

// Fetch Order Details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

// Check if already paid
if ($order['status'] !== 'pending') {
    redirect('payment_success.php?existing=1&order_id=' . $order_id);
}

try {
    // Capture the payment with Afterpay API
    $auth = base64_encode(AFTERPAY_MERCHANT_ID . ':' . AFTERPAY_SECRET_KEY);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, AFTERPAY_API_URL . '/payments/capture');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . $auth,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'token' => $order_token
    ]));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 201 && $http_code !== 200) {
        // Payment capture failed
        error_log("Afterpay capture failed: " . $response);
        redirect('payment_cancel.php?order_id=' . $order_id . '&error=capture_failed');
    }
    
    $afterpay_response = json_decode($response, true);
    
    $conn->begin_transaction();
    
    // Insert Payment Record
    $transaction_id = isset($afterpay_response['id']) ? $afterpay_response['id'] : 'AFTERPAY-' . time();
    $payment_status = 'COMPLETED';
    $payment_method = 'afterpay';
    $amount = $order['total_price'];
    $currency = AFTERPAY_CURRENCY;
    $payer_id = isset($afterpay_response['consumer']['email']) ? $afterpay_response['consumer']['email'] : 'AFTERPAY-USER';
    
    $stmt = $conn->prepare("INSERT INTO payments (order_id, transaction_id, payer_id, amount, currency, payment_status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsss", $order_id, $transaction_id, $payer_id, $amount, $currency, $payment_status, $payment_method);
    $stmt->execute();
    
    // Update Order Status
    $update_stmt = $conn->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ?");
    $update_stmt->bind_param("i", $order_id);
    $update_stmt->execute();
    
    $conn->commit();
    
    // Redirect to success page
    redirect('payment_success.php?order_id=' . $order_id . '&payment_method=afterpay&transaction_id=' . urlencode($transaction_id));
    
} catch (Exception $e) {
    $conn->rollback();
    error_log("Afterpay payment error: " . $e->getMessage());
    redirect('payment_cancel.php?order_id=' . $order_id . '&error=processing_failed');
}
?>
