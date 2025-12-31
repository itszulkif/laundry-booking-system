<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Handle existing payment view (redirected from payment.php if already paid)
if (isset($_GET['existing']) && isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
    // Fetch order code
    $stmt = $conn->prepare("SELECT order_code FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $order_code = $res ? $res['order_code'] : 'Unknown';
    $transaction_id = null;
    $payment_method = 'existing';
    $payment_status = 'completed';
    
    // Display success without re-processing
} elseif (isset($_GET['order_id']) && isset($_GET['payment_method'])) {
    // Handle PayID and Afterpay redirects (already processed)
    $order_id = (int)$_GET['order_id'];
    $payment_method = $_GET['payment_method'];
    $transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : null;
    $payment_status = isset($_GET['payment_status']) ? $_GET['payment_status'] : 'completed';
    
    // Fetch order code
    $stmt = $conn->prepare("SELECT order_code FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $order_code = $res ? $res['order_code'] : 'Unknown';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process new payment
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $transaction_id = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : '';
    $payer_id = isset($_POST['payer_id']) ? $_POST['payer_id'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
    $currency = isset($_POST['currency']) ? $_POST['currency'] : 'USD';
    $status = isset($_POST['status']) ? $_POST['status'] : 'COMPLETED';
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'paypal';
    
    if ($order_id === 0 || empty($transaction_id)) {
        redirect('../index.php');
    }
    
    try {
        $conn->begin_transaction();
        
        // 1. Insert Payment Record
        $stmt = $conn->prepare("INSERT INTO payments (order_id, transaction_id, payer_id, amount, currency, payment_status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdsss", $order_id, $transaction_id, $payer_id, $amount, $currency, $status, $payment_method);
        $stmt->execute();
        
        // 2. Update Order Status
        $update_stmt = $conn->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ?");
        $update_stmt->bind_param("i", $order_id);
        $update_stmt->execute();
        
        // Fetch order code for display
        $code_stmt = $conn->prepare("SELECT order_code FROM orders WHERE id = ?");
        $code_stmt->bind_param("i", $order_id);
        $code_stmt->execute();
        $order_code = $code_stmt->get_result()->fetch_assoc()['order_code'];
        
        $conn->commit();
        
    } catch (Exception $e) {
        $conn->rollback();
        die("Error recording payment: " . $e->getMessage());
    }
} else {
    redirect('../index.php');
}
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="antigravity-card p-5 animate__animated animate__zoomIn">
                <div class="mb-4 <?php echo ($payment_status == 'pending') ? 'text-warning' : 'text-success'; ?>">
                    <?php if($payment_status == 'pending'): ?>
                        <i class="bi bi-clock-history" style="font-size: 5rem;"></i>
                    <?php else: ?>
                        <i class="bi bi-check-circle-fill" style="font-size: 5rem;"></i>
                    <?php endif; ?>
                </div>
                <h2 class="fw-bold mb-3"><?php echo ($payment_status == 'pending') ? 'Payment Pending Verification' : 'Payment Successful!'; ?></h2>
                <p class="text-muted mb-4">
                    <?php 
                    if($payment_status == 'pending') {
                        echo "Thank you! We have received your request. Your booking will be confirmed once we verify your payment.";
                    } else {
                        echo "Thank you! Your transaction has been completed and your booking is confirmed.";
                    }
                    ?>
                </p>
                
                <div class="bg-light p-3 rounded mb-4">
                    <small class="text-muted text-uppercase fw-bold">Order Code</small>
                    <h3 class="fw-bold text-primary m-0"><?php echo htmlspecialchars($order_code); ?></h3>
                </div>
                
                <?php if(isset($transaction_id)): ?>
                <div class="mb-4">
                    <small class="text-muted">Transaction ID: <?php echo htmlspecialchars($transaction_id); ?></small>
                </div>
                <?php endif; ?>
                
                <a href="../index.php" class="btn btn-primary-soft">Return Home</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
