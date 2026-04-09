<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/header.php';

// Get Data from previous step
$city_id = isset($_POST['city_id']) ? (int)$_POST['city_id'] : (isset($_GET['city_id']) ? (int)$_GET['city_id'] : 0);
$service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : (isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0);
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : (isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1);
$staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : (isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0);
$quantity = 1; // Default to 1, unused now

if ($city_id === 0 || $service_id === 0 || $staff_id === 0) {
    // If missing data, redirect back to staff selection
    redirect("staff.php?city_id=$city_id&service_id=$service_id");
}

// Fetch Staff Details including Daily Rate
$staff_query = "SELECT * FROM staff WHERE id = $staff_id";
$staff_result = $conn->query($staff_query);
$staff = $staff_result->fetch_assoc();

// Service details for name only
$service_query = "SELECT name, duration FROM services WHERE id = $service_id";
$service_result = $conn->query($service_query);
$service = $service_result->fetch_assoc();

// Calculate Hourly Rate from Daily Rate (Assuming 8 hour day)
$hourly_rate = $staff['daily_rate'] / 8;
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
      <?php if (isset($_SESSION['booking_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Booking Conflict!</strong> <?php echo htmlspecialchars($_SESSION['booking_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

    

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
                            <a href="staff.php?city_id=<?php echo $city_id; ?>&service_id=<?php echo $service_id; ?>" class="btn btn-sm btn-outline-primary">Change</a>
                        </div>
                    </div>
                </div>


                
                <!-- Duration Selection -->
                <div class="antigravity-card p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-hourglass-split text-primary me-2"></i> Select Duration</h5>
                    <select class="form-select form-control-clean" name="duration" id="durationSelect" required>
                        <option value="" disabled selected>Select duration...</option>
                        <option value="4">Half Day (4 Hours) - PKR <?php echo number_format($hourly_rate * 4, 2); ?></option>
                        <option value="8">Full Day (8 Hours) - PKR <?php echo number_format($hourly_rate * 8, 2); ?></option>
                    </select>
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
                                        $time_str = sprintf('%02d:%02d', $h, $m);
                                        // Display in 12h format
                                        $display_time = date('h:i A', strtotime($time_str));
                                        
                                        // Ensure we don't go past working end time
                                        if (strtotime($time_str) >= strtotime($staff['working_end'])) break;
                                        echo "<option value='$time_str'>$display_time</option>";
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
                            <span class="text-muted"><i class="bi bi-check text-success"></i></span>
                        </div>
                    </div>
                    
                    <hr>
                    

                    
                    <button type="submit" class="btn btn-primary-soft w-100">
                        Enter Details <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const startTime = document.getElementById('startTime');
    const dateInput = document.getElementById('dateInput');
    const durationSelect = document.getElementById('durationSelect');
    const form = document.getElementById('scheduleForm');
    const availabilityMessage = document.getElementById('availabilityMessage');

    
    let currentBookings = [];
    const CITY_ID = <?php echo $city_id; ?>;
    const STAFF_ID = <?php echo $staff_id; ?>;
    const HOURLY_RATE = <?php echo $hourly_rate; ?>;
    
    // Staff Working Schedule
    const WORKING_DAYS = <?php echo json_encode(explode(',', $staff['working_days'])); ?>;
    const WORKING_START = '<?php echo $staff['working_start']; ?>';
    const WORKING_END = '<?php echo $staff['working_end']; ?>';

    // Duration change handler
    durationSelect.addEventListener('change', function() {

        if (dateInput.value) {
            checkAvailability();
        }
    });



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

    // Check if selected time conflicts
    function checkAvailability() {
        const selectedStart = startTime.value;
        const durationHours = parseInt(durationSelect.value);
        
        if (!selectedStart || !durationHours) return;

        availabilityMessage.innerHTML = '';
        startTime.classList.remove('is-invalid');

        let isAvailable = true;
        let conflictReason = '';

        const selectedStartMins = timeToMinutes(selectedStart);
        const selectedEndMins = selectedStartMins + (durationHours * 60);

        // Check if ends after working hours (handling cross-midnight working schedule)
        const workingStartMins = timeToMinutes(WORKING_START);
        const workingEndMins = timeToMinutes(WORKING_END);
        
        let effectiveWorkingEnd = workingEndMins;
        if (workingEndMins <= workingStartMins) {
            effectiveWorkingEnd += 1440; // Working ends on the next day
        }

        if (selectedEndMins > effectiveWorkingEnd) {
            isAvailable = false;
            conflictReason = 'Booking exceeds working hours (' + WORKING_END + ')';
        }

        if (isAvailable) {
            // Check against existing bookings (capped by API for the current day)
            for (const booking of currentBookings) {
                const bookingStartMins = timeToMinutes(booking.start_time);
                let bookingEndMins = timeToMinutes(booking.end_time);
                
                // If end_time is 23:59 or less than start_time (without capping), handle it
                if (bookingEndMins <= bookingStartMins && bookingEndMins > 0) {
                   bookingEndMins += 1440;
                } else if (booking.end_time === '23:59') {
                   bookingEndMins = 1440; // Full cap at midnight
                }
                
                // Conflict if overlap
                if (selectedStartMins < bookingEndMins && selectedEndMins > bookingStartMins) {
                    isAvailable = false;
                    conflictReason = `the slot is already booked (${booking.start_time} to ${booking.end_time})`;
                    break;
                }
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
        if (!startTime.value || !durationSelect.value) {
            e.preventDefault();
            alert('Please select a duration and start time');
            return;
        }
        
        if (!checkAvailability()) {
            e.preventDefault();
        }
    });
});

// Show Booking Error if exists in session
<?php if (isset($_SESSION['booking_error'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: '<span class="text-danger">Slot Already Booked</span>',
        html: `<div class='text-center'>
                <p class='mb-4'>We're sorry, but the selected time slot <strong>(<?php echo htmlspecialchars($_SESSION['booking_error']); ?>)</strong> is no longer available.</p>
                <div class='alert bg-light border-0 py-3 mb-4'>
                    <p class='text-muted mb-0 small'><i class="bi bi-calendar-x me-2"></i> This staff member is now fully booked for that specific period.</p>
                </div>
                <p class='fw-bold text-primary'>Please choose a different date or time to proceed.</p>
               </div>`,
        icon: 'error',
        confirmButtonText: 'Try Another Slot',
        confirmButtonColor: '#0d6efd',
        background: '#ffffff',
        backdrop: `rgba(0,0,123,0.4)`,
        showClass: {
            popup: 'animate__animated animate__zoomInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
        }
    });
});
<?php unset($_SESSION['booking_error']); endif; ?>
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
