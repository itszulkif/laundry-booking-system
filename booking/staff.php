<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Get Data from previous step
$city_id = isset($_POST['city_id']) ? (int)$_POST['city_id'] : (isset($_GET['city_id']) ? (int)$_GET['city_id'] : 0);
$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : (isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0);
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : (isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1);

if ($city_id === 0 || $service_id === 0) {
    // If missing data, redirect back to services
    redirect("services.php?city_id=$city_id");
}

// Fetch Staff for this City who can perform THIS service
$staff_query = "SELECT s.* FROM staff s 
                JOIN city_staff cs ON s.id = cs.staff_id
                WHERE cs.city_id = $city_id 
                AND s.status = 'available'
                AND s.id IN (
                    SELECT staff_id FROM staff_services 
                    WHERE service_id = $service_id
                )";
$staff_result = $conn->query($staff_query);

// Calculate Total Price (for summary)
$price_query = "SELECT id, price, name, price_unit FROM services WHERE id = $service_id";
$price_result = $conn->query($price_query);
$service = $price_result->fetch_assoc();

$total_price = $service['price'] * $quantity;
?>

<div class="container py-5">
    <!-- Step Indicator -->
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <div class="step-indicator">
                <div class="step-item completed"><i class="bi bi-geo-alt"></i></div>
                <div class="step-item completed"><i class="bi bi-basket"></i></div>
                <div class="step-item active">3</div>
                <div class="step-item">4</div>
                <div class="step-item">5</div>
            </div>
            <div class="text-center">
                <h2 class="fw-bold">Choose Professional</h2>
                <p class="text-muted">Select your preferred staff member.</p>
            </div>
        </div>
    </div>

    <form action="schedule.php" method="GET" id="staffForm">
        <input type="hidden" name="city_id" value="<?php echo $city_id; ?>">
        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
        <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
        
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                
                <!-- Staff Selection -->
                <div class="antigravity-card p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person-badge text-primary me-2"></i> Available Staff</h5>
                    <div class="row g-3">
                        <?php if ($staff_result->num_rows > 0): ?>
                            <?php while($staff = $staff_result->fetch_assoc()): ?>
                                <div class="col-md-6">
                                    <label class="border rounded p-3 d-flex align-items-center cursor-pointer h-100 w-100 staff-card position-relative" id="staff-card-<?php echo $staff['id']; ?>">
                                        <input type="radio" name="staff_id" value="<?php echo $staff['id']; ?>" class="form-check-input position-absolute top-0 end-0 m-3" required>
                                        <img src="<?php echo $base_url; ?>/assets/img/<?php echo $staff['avatar']; ?>" 
                                             onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($staff['name']); ?>&background=random'"
                                             class="rounded-circle me-3" width="60" height="60" alt="Avatar">
                                        <div>
                                            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($staff['name']); ?></h6>
                                            <small class="text-muted d-block mb-1">
                                                <?php echo date('h:i A', strtotime($staff['working_start'])); ?> - <?php echo date('h:i A', strtotime($staff['working_end'])); ?>
                                            </small>
                                            <div class="text-warning small">
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-fill"></i>
                                                <i class="bi bi-star-half"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning">No staff available for this service in this city.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Summary Sidebar -->
            <div class="col-lg-4">
                <div class="antigravity-card p-4 sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4">Booking Summary</h5>
                    
                    <div class="mb-3">
                        <span class="text-muted small d-block mb-2">Service</span>
                        <div class="d-flex justify-content-between mb-1 small">
                            <span><?php echo htmlspecialchars($service['name']); ?></span>
                            <span class="text-muted">x<?php echo $quantity; ?></span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-primary">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-soft w-100">
                        Select Date & Time <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('staffForm');

    form.addEventListener('submit', function(e) {
        const staffSelected = document.querySelector('input[name="staff_id"]:checked');
        if (!staffSelected) {
            e.preventDefault();
            alert('Please select a staff member');
            return;
        }
    });
});
</script>

<style>
.staff-card {
    transition: all 0.2s;
    border: 2px solid #eee;
}

.staff-card:has(input:checked) {
    border-color: var(--primary-color);
    background-color: #f8fbff;
}
</style>

<?php require_once '../includes/footer.php'; ?>
