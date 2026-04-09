<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$order_code = '';

if ($order_id > 0) {
    $stmt = $conn->prepare("SELECT order_code FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $order_code = $row['order_code'];
    }
}

if (empty($order_code)) {
    redirect('../index.php');
}
?>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="antigravity-card p-5 animate__animated animate__zoomIn">
                <div class="mb-4 text-success">
                    <i class="bi bi-check-circle-fill" style="font-size: 5rem;"></i>
                </div>
                <h2 class="fw-bold mb-3">Booking Confirmed!</h2>
                <p class="text-muted mb-4">
                    Thank you! Your booking has been successfully placed. We will contact you shortly to confirm the details.
                </p>
                
                <div class="bg-light p-3 rounded mb-4">
                    <small class="text-muted text-uppercase fw-bold">Order Code</small>
                    <h3 class="fw-bold text-primary m-0"><?php echo htmlspecialchars($order_code); ?></h3>
                </div>
                
                <a href="../index.php" class="btn btn-primary-soft">Return Home</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
