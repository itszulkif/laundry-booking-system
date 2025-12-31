<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    redirect('payments.php');
}

// Fetch Payment Details
$query = "SELECT p.*, o.order_code, o.customer_name, o.customer_email, o.customer_phone, o.booking_date, s.name as service_name 
          FROM payments p 
          LEFT JOIN orders o ON p.order_id = o.id 
          LEFT JOIN order_items oi ON o.id = oi.order_id
          LEFT JOIN services s ON oi.service_id = s.id
          WHERE p.id = $id";
$result = $conn->query($query);
$payment = $result->fetch_assoc();

if (!$payment) {
    redirect('payments.php');
}
// Handle Manual Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_payment') {
    try {
        $conn->begin_transaction();
        
        // 1. Update Payment Status
        $update_payment = $conn->prepare("UPDATE payments SET payment_status = 'COMPLETED' WHERE id = ?");
        $update_payment->bind_param("i", $id);
        $update_payment->execute();
        
        // 2. Update Order Status
        $update_order = $conn->prepare("UPDATE orders o JOIN payments p ON p.order_id = o.id SET o.status = 'confirmed' WHERE p.id = ?");
        $update_order->bind_param("i", $id);
        $update_order->execute();
        
        $conn->commit();
        echo "<script>alert('Payment verified and order confirmed successfully!'); window.location.href='payment_details.php?id=$id';</script>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error verifying payment: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="payments.php" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i> Back to Payments</a>
        <h2 class="fw-bold text-primary mt-1">Transaction Details</h2>
    </div>
    <div>
        <?php if($payment['payment_status'] === 'PENDING'): ?>
        <form method="POST" style="display: inline-block;">
            <input type="hidden" name="action" value="verify_payment">
            <button type="submit" class="btn btn-success me-2" onclick="return confirm('Are you sure you want to verify this payment? This will confirm the order.')">
                <i class="bi bi-check-lg"></i> Verify & Confirm Payment
            </button>
        </form>
        <?php endif; ?>
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0">Payment Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Transaction ID</label>
                        <p class="fs-5 font-monospace"><?php echo htmlspecialchars($payment['transaction_id']); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <label class="text-muted small text-uppercase fw-bold">Status</label>
                        <div>
                            <?php 
                            $status_class = 'bg-secondary';
                            if ($payment['payment_status'] == 'COMPLETED') $status_class = 'bg-success';
                            elseif ($payment['payment_status'] == 'PENDING') $status_class = 'bg-warning text-dark';
                            elseif ($payment['payment_status'] == 'FAILED') $status_class = 'bg-danger';
                            ?>
                            <span class="badge <?php echo $status_class; ?> fs-6"><?php echo htmlspecialchars($payment['payment_status']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="text-muted small text-uppercase fw-bold">Amount</label>
                        <p class="fw-bold">$<?php echo number_format($payment['amount'], 2); ?> <?php echo htmlspecialchars($payment['currency']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small text-uppercase fw-bold">Payment Method</label>
                        <p class="text-capitalize"><?php echo htmlspecialchars($payment['payment_method']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small text-uppercase fw-bold">Date</label>
                        <p><?php echo date('M d, Y H:i:s', strtotime($payment['created_at'])); ?></p>
                    </div>
                </div>
                
                <div class="mb-0">
                    <label class="text-muted small text-uppercase fw-bold">Payer ID</label>
                    <p class="font-monospace text-muted"><?php echo htmlspecialchars($payment['payer_id']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0">Audit Log</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border mb-0">
                    <i class="bi bi-info-circle me-2"></i> Payment was recorded automatically via PayPal webhook/callback on <?php echo date('M d, Y H:i:s', strtotime($payment['created_at'])); ?>.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold m-0">Order Details</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Order Code</span>
                    <a href="orders.php?search=<?php echo htmlspecialchars($payment['order_code']); ?>" class="fw-bold text-decoration-none"><?php echo htmlspecialchars($payment['order_code']); ?></a>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Service</span>
                    <span class="fw-bold"><?php echo htmlspecialchars($payment['service_name']); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Booking Date</span>
                    <span><?php echo date('M d, Y', strtotime($payment['booking_date'])); ?></span>
                </div>
                <hr>
                <h6 class="fw-bold mb-3">Customer</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded-circle p-2 me-3">
                        <i class="bi bi-person fs-4 text-primary"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($payment['customer_name']); ?></p>
                        <small class="text-muted">Customer</small>
                    </div>
                </div>
                <div class="mb-2">
                    <i class="bi bi-envelope me-2 text-muted"></i> <?php echo htmlspecialchars($payment['customer_email']); ?>
                </div>
                <div class="mb-0">
                    <i class="bi bi-telephone me-2 text-muted"></i> <?php echo htmlspecialchars($payment['customer_phone']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
