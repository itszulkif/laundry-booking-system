<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Get Data from previous step
$city_id = isset($_POST['city_id']) ? (int)$_POST['city_id'] : (isset($_GET['city_id']) ? (int)$_GET['city_id'] : 0);
$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : (isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0);
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : (isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1);
$staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : (isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0);

if ($city_id === 0 || $service_id === 0 || $staff_id === 0) {
    // If missing data, redirect back to staff selection
    redirect("staff.php?city_id=$city_id&service_id=$service_id&quantity=$quantity");
}

// Fetch Staff Details
$staff_query = "SELECT * FROM staff WHERE id = $staff_id";
$staff_result = $conn->query($staff_query);
$staff = $staff_result->fetch_assoc();

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
                <div class="step-item completed"><i class="bi bi-person-badge"></i></div>
                <div class="step-item active">4</div>
                <div class="step-item">5</div>
            </div>
            <div class="text-center">
                <h2 class="fw-bold">Schedule Service</h2>
                <p class="text-muted">Choose a start time for <span class="text-primary fw-bold"><?php echo htmlspecialchars($staff['name']); ?></span>.</p>
            </div>
        </div>
    </div>

    <form action="details.php" method="POST" id="scheduleForm">
        <input type="hidden" name="city_id" value="<?php echo $city_id; ?>">
        <input type="hidden" name="service_id" value="<?php echo $service_id; ?>">
        <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
        <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
        
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                
                <!-- Selected Staff Summary -->
                <div class="antigravity-card p-4 mb-4 bg-light border-0">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo $base_url; ?>/assets/img/<?php echo $staff['avatar']; ?>" 
                             onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($staff['name']); ?>&background=random'"
                             class="rounded-circle me-3" width="50" height="50" alt="Avatar">
                        <div>
                            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($staff['name']); ?></h6>
                            <small class="text-muted d-block">
                                Working Hours: <?php echo date('h:i A', strtotime($staff['working_start'])); ?> - <?php echo date('h:i A', strtotime($staff['working_end'])); ?>
                            </small>
                        </div>
                        <div class="ms-auto">
                            <a href="staff.php?city_id=<?php echo $city_id; ?>&service_id=<?php echo $service_id; ?>&quantity=<?php echo $quantity; ?>" class="btn btn-sm btn-outline-primary">Change</a>
                        </div>
                    </div>
                </div>
                
                <!-- Date Selection -->
                <div class="antigravity-card p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-calendar-event text-primary me-2"></i> Select Date</h5>
                    <input type="date" class="form-control form-control-clean" name="date" id="dateInput" required 
                           min="<?php echo date('Y-m-d'); ?>">
                </div>

                <!-- Time Selection -->
                <div class="antigravity-card p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock text-primary me-2"></i> Select Start Time</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small text-muted fw-bold">Start Time</label>
                            <select class="form-select form-control-clean" name="start_time" id="startTime" required>
                                <option value="">-- Select Start Time --</option>
                                <?php 
                                $start_hour = (int)date('H', strtotime($staff['working_start']));
                                $end_hour = (int)date('H', strtotime($staff['working_end']));
                                
                                for ($h = $start_hour; $h < $end_hour; $h++) {
                                    for ($m = 0; $m < 60; $m += 30) {
                                        $time = sprintf('%02d:%02d', $h, $m);
                                        // Ensure we don't go past working end time
                                        if (strtotime($time) >= strtotime($staff['working_end'])) break;
                                        echo "<option value='$time'>$time</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="availabilityMessage" class="mt-3"></div>
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
                        Enter Details <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startTime = document.getElementById('startTime');
    const dateInput = document.getElementById('dateInput');
    const form = document.getElementById('scheduleForm');
    const availabilityMessage = document.getElementById('availabilityMessage');
    
    let currentBookings = [];
    const CITY_ID = <?php echo $city_id; ?>;
    const STAFF_ID = <?php echo $staff_id; ?>;
    
    // Staff Working Schedule
    const WORKING_DAYS = <?php echo json_encode(explode(',', $staff['working_days'])); ?>; // ['Mon', 'Tue', ...]
    const WORKING_START = '<?php echo $staff['working_start']; ?>';
    const WORKING_END = '<?php echo $staff['working_end']; ?>';

    // Disable non-working days in date picker
    dateInput.addEventListener('input', function() {
        const date = new Date(this.value);
        const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
        
        if (!WORKING_DAYS.includes(dayName)) {
            alert(`This staff member does not work on ${dayName}s. Please select a valid working day.`);
            this.value = ''; // Clear invalid date
            return;
        }
        
        // If valid, fetch availability
        fetchAvailability(this.value);
    });

    function fetchAvailability(date) {
        fetch(`../api/check_availability.php?city_id=${CITY_ID}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                // Filter bookings for this specific staff member
                currentBookings = data.filter(booking => booking.staff_id == STAFF_ID);
                checkAvailability();
            })
            .catch(err => console.error('Error fetching availability:', err));
    }

    // Helper function to convert time string to minutes
    function timeToMinutes(time) {
        const [hours, minutes] = time.split(':').map(Number);
        return hours * 60 + minutes;
    }

    // Check if selected time conflicts with existing bookings
    function checkAvailability() {
        const selectedStart = startTime.value;
        
        availabilityMessage.innerHTML = '';
        startTime.classList.remove('is-invalid');

        if (!selectedStart) return;

        let isAvailable = true;
        let conflictReason = '';

        // Check against existing bookings (including their 30-min break)
        for (const booking of currentBookings) {
            const selectedStartMins = timeToMinutes(selectedStart);
            const selectedEndMins = selectedStartMins + 30; // Our booking + 30 min break
            const bookingStartMins = timeToMinutes(booking.start_time);
            const bookingEndMins = timeToMinutes(booking.break_end_time);
            
            // Check if our time slot overlaps with existing booking + break
            if (selectedStartMins < bookingEndMins && selectedEndMins > bookingStartMins) {
                isAvailable = false;
                conflictReason = `Staff is busy from ${booking.start_time} to ${booking.break_end_time} (including break)`;
                break;
            }
        }

        if (!isAvailable) {
            startTime.classList.add('is-invalid');
            availabilityMessage.innerHTML = `<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-circle me-2"></i>${conflictReason}</div>`;
        } else {
             availabilityMessage.innerHTML = `<div class="alert alert-success mb-0"><i class="bi bi-check-circle me-2"></i>Time slot is available!</div>`;
        }
        
        return isAvailable;
    }

    startTime.addEventListener('change', checkAvailability);

    form.addEventListener('submit', function(e) {
        if (!startTime.value) {
            e.preventDefault();
            alert('Please select a start time');
            return;
        }
        
        if (!checkAvailability()) {
            e.preventDefault();
            // Alert is already shown by checkAvailability
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
