<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/payid_config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php');
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

if ($order_id === 0) {
    redirect('../index.php');
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
    $conn->begin_transaction();
    
    // Generate unique transaction ID for PayID
    $transaction_id = PAYID_TRANSACTION_PREFIX . time() . '-' . $order_id;
    
    // Insert Payment Record
    $payment_status = 'PENDING'; // PayID is manual confirmation
    $payment_method = 'payid';
    $amount = $order['total_price'];
    $currency = 'USD';
    $payer_id = 'PAYID-MANUAL'; // Manual payment confirmation
    
    $stmt = $conn->prepare("INSERT INTO payments (order_id, transaction_id, payer_id, amount, currency, payment_status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsss", $order_id, $transaction_id, $payer_id, $amount, $currency, $payment_status, $payment_method);
    $stmt->execute();
    
    // NOTE: We do NOT update the order status to 'confirmed' here.
    // PayID requires manual verification by admin.
    
    $conn->commit();
    
    // Redirect to success page with pending status
    redirect('payment_success.php?order_id=' . $order_id . '&payment_method=payid&transaction_id=' . urlencode($transaction_id) . '&payment_status=pending');
    
} catch (Exception $e) {
    $conn->rollback();
    die("Error processing PayID payment: " . $e->getMessage());
}
?>
