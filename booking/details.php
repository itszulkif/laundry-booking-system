<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Get Data from previous steps
$city_id = isset($_POST['city_id']) ? (int)$_POST['city_id'] : 0;
$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$date = isset($_POST['date']) ? $_POST['date'] : '';
$start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
$staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 4; // Default to 4 hours if missing

if ($city_id === 0 || $service_id === 0 || empty($date) || empty($start_time) || $staff_id === 0) {
    redirect('../index.php');
}

// Fetch Staff Details (Daily Rate)
$staff_query = "SELECT name, daily_rate FROM staff WHERE id = $staff_id";
$staff_result = $conn->query($staff_query);
$staff_data = $staff_result->fetch_assoc();
$staff_name = $staff_data['name'];
$daily_rate = $staff_data['daily_rate'];

// Service Name Only
$service_query = "SELECT name, gst_rate FROM services WHERE id = $service_id";
$price_result = $conn->query($service_query);
$service = $price_result->fetch_assoc();
$gst_rate = isset($service['gst_rate']) ? (float)$service['gst_rate'] : 10.00;

// Calculate Price based on Duration
$hourly_rate = $daily_rate / 8;
$subtotal = $hourly_rate * $duration;
$tax_amount = $subtotal * ($gst_rate / 100);
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
                <div class="step-item active">4</div>
                <div class="step-item">5</div>
            </div>
            <div class="text-center">
                <h2 class="fw-bold">Your Details</h2>
                <p class="text-muted">Where should we pick up your laundry?</p>
            </div>
        </div>
    </div>

    <form action="process_booking.php" method="POST">
        <!-- Hidden Fields to pass data forward -->
        <input type="hidden" name="city_id" value="<?php echo $city_id; ?>">
        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
        <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
        <input type="hidden" name="date" value="<?php echo $date; ?>">
        <input type="hidden" name="start_time" value="<?php echo $start_time; ?>">
        <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
        <input type="hidden" name="duration" value="<?php echo $duration; ?>">
        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

        <div class="row g-4">
            <!-- Main Form -->
            <div class="col-lg-8">
                <div class="antigravity-card p-4 mb-4">
                    <h5 class="fw-bold mb-4">Contact Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Full Name</label>
                            <input type="text" class="form-control form-control-clean" name="customer_name" required placeholder="Ali Khan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Phone Number</label>
                            <input type="tel" class="form-control form-control-clean" name="customer_phone" required placeholder="+92 300 1234567">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Email Address</label>
                            <input type="email" class="form-control form-control-clean" name="customer_email" required placeholder="name@email.com">
                        </div>
                    </div>
                </div>

                <div class="antigravity-card p-4 mb-4">
                    <h5 class="fw-bold mb-4">Address Details</h5>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Street Address</label>
                        <textarea class="form-control form-control-clean" name="street_address" rows="2" required placeholder="House #, Street Name, Area"></textarea>
                    </div>
                    <!-- Delivery address toggle removed as per request -->
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small">Special Instructions</label>
                        <textarea class="form-control form-control-clean" name="special_instructions" rows="2" placeholder='Please call before arriving'></textarea>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="col-lg-4">
                <div class="antigravity-card p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">Booking Summary</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Date</span>
                            <span class="small fw-bold"><?php echo date('M d, Y', strtotime($date)); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Time</span>
                            <span class="small fw-bold"><?php echo $start_time; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Staff</span>
                            <span class="small fw-bold"><?php echo htmlspecialchars($staff_name); ?></span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Subtotal</span>
                        <span class="small fw-bold">PKR <?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">GST (<?php echo $gst_rate; ?>%)</span>
                        <span class="small fw-bold">PKR <?php echo number_format($tax_amount, 2); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4 pt-2 border-top">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-primary">PKR <?php echo number_format($total_price, 2); ?></span>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-soft w-100">
                        Review & Confirm <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('sameAddress').addEventListener('change', function() {
    const deliveryGroup = document.getElementById('deliveryAddressGroup');
    const deliveryInput = deliveryGroup.querySelector('textarea');
    
    if (this.checked) {
        deliveryGroup.style.display = 'none';
        deliveryInput.required = false;
        deliveryInput.value = '';
    } else {
        deliveryGroup.style.display = 'block';
        deliveryInput.required = true;
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
