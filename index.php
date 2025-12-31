<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Fetch cities for the dropdown
$cities_query = "SELECT * FROM cities ORDER BY name ASC";
$cities_result = $conn->query($cities_query);
?>

<!-- Hero Section -->
<!-- Hero Section -->
<section class="hero-section d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <!-- Logo -->
                <div class="mb-4 animate__animated animate__fadeInDown">
                    <a class="text-decoration-none" href="<?php echo $base_url; ?>/index.php">
                        <img src="assets/images/afe35db6-37e4-471e-92e8-25c35ce8a7bf.png" alt="Dr. Spin Logo" class="img-fluid" style="max-height: 120px;">
                    </a>
                </div>

                <!-- Paragraph -->
                <p class="lead text-muted mb-5 animate__animated animate__fadeInUp animate__delay-1s px-md-5">
                    Dr. Spin - Professional laundry service at your doorstep. Experience the antigravity of cleanliness with our premium wash & fold, dry cleaning, and ironing services.
                
                <!-- Booking Form -->
                <div class="antigravity-card p-4 p-md-5 animate__animated animate__fadeInUp animate__delay-2s mx-auto w-100">
                    <form action="booking/services.php" method="GET">
                        <label for="city" class="form-label fw-bold text-uppercase small text-muted mb-3 d-block text-start">Select Your City</label>
                        <div class="input-group input-group-lg mb-4">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                            <select class="form-select form-control-clean border-start-0 ps-0 fs-5" id="city" name="city_id" required style="height: 60px;">
                                <option value="" selected disabled>Choose your location...</option>
                                <?php if ($cities_result->num_rows > 0): ?>
                                    <?php while($city = $cities_result->fetch_assoc()): ?>
                                        <option value="<?php echo $city['id']; ?>"><?php echo $city['name']; ?></option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="" disabled>No cities available yet</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary-soft w-100 py-3 fs-5 fw-bold shadow-sm">
                            Start Booking <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>



<?php require_once 'includes/footer.php'; ?>
