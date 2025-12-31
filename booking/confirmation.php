<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Get Data
$data = $_POST;

if (empty($data['customer_email'])) {
    redirect('../index.php');
}

$service_id = (int)$data['service_id'];
$quantity = (int)$data['quantity'];

// Re-calculate total to prevent tampering
$price_query = "SELECT id, price, name, price_unit FROM services WHERE id = $service_id";
$price_result = $conn->query($price_query);
$service = $price_result->fetch_assoc();

$subtotal = $service['price'] * $quantity;
$tax_amount = $subtotal * 0.10;
$total_price = $subtotal + $tax_amount;
?>

<div class="container py-5">
    <!-- Step Indicator -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <div class="step-indicator">
                <div class="step-item completed"><i class="bi bi-geo-alt"></i></div>
                <div class="step-item completed"><i class="bi bi-basket"></i></div>
                <div class="step-item completed"><i class="bi bi-calendar-check"></i></div>
                <div class="step-item completed"><i class="bi bi-person-lines-fill"></i></div>
                <div class="step-item active">5</div>
            </div>
            <div class="text-center">
                <h2 class="fw-bold">Review & Confirm</h2>
                <p class="text-muted">Please review your booking details before submitting.</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="process_booking.php" method="POST">
                <!-- Pass all data forward -->
                <?php foreach($data as $key => $value): ?>
                    <?php if(!is_array($value)): ?>
                        <input type="hidden" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="antigravity-card p-4 mb-4">
                    <h5 class="fw-bold mb-4">Order Summary</h5>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted small text-uppercase fw-bold">Customer</h6>
                            <p class="mb-1 fw-bold"><?php echo htmlspecialchars($data['customer_name']); ?></p>
                            <p class="mb-1 small"><?php echo htmlspecialchars($data['customer_email']); ?></p>
                            <p class="mb-0 small"><?php echo htmlspecialchars($data['customer_phone']); ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted small text-uppercase fw-bold">Schedule</h6>
                            <p class="mb-1 fw-bold"><?php echo date('l, M d, Y', strtotime($data['date'])); ?></p>
                            <p class="mb-0 text-primary fw-bold"><?php echo htmlspecialchars($data['start_time']); ?></p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-muted small text-uppercase fw-bold mb-3">Service</h6>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <?php echo htmlspecialchars($service['name']); ?>
                                <small class="text-muted d-block"><?php echo $quantity; ?> <?php echo $service['price_unit'] == 'kg' ? 'kg' : 'items'; ?> x $<?php echo number_format($service['price'], 2); ?></small>
                            </div>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                GST (10%)
                            </div>
                            <span>$<?php echo number_format($tax_amount, 2); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold fs-5">
                            Total
                            <span class="text-primary">$<?php echo number_format($total_price, 2); ?></span>
                        </li>
                    </ul>

                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase fw-bold">Pickup Address</h6>
                        <p class="small text-muted bg-light p-3 rounded"><?php echo nl2br(htmlspecialchars($data['pickup_address'])); ?></p>
                    </div>

                    <?php if(!empty($data['delivery_address'])): ?>
                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase fw-bold">Delivery Address</h6>
                        <p class="small text-muted bg-light p-3 rounded"><?php echo nl2br(htmlspecialchars($data['delivery_address'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label small text-muted" for="terms">
                            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary-soft w-100 py-3 fs-5">
                        Complete Booking <i class="bi bi-check-circle-fill ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
