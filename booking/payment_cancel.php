<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="antigravity-card p-5 animate__animated animate__fadeIn">
                <div class="mb-4 text-danger">
                    <i class="bi bi-x-circle-fill" style="font-size: 5rem;"></i>
                </div>
                <h2 class="fw-bold mb-3">Payment Cancelled</h2>
                <p class="text-muted mb-4">You have cancelled the payment process. Your booking is still pending but not confirmed.</p>
                
                <div class="d-grid gap-2 col-8 mx-auto">
                    <?php if($order_id > 0): ?>
                    <a href="payment.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">Try Again</a>
                    <?php endif; ?>
                    <a href="../index.php" class="btn btn-outline-secondary">Return Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
