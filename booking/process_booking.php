<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php');
}

$data = $_POST;
$service_id = (int)$data['service_id'];
$quantity = (int)$data['quantity'];

try {
    $conn->begin_transaction();

    // 0. Fetch Staff Details & Daily Rate
    $staff_id = (int)$data['staff_id'];
    $staff_query = "SELECT daily_rate FROM staff WHERE id = $staff_id";
    $staff_result = $conn->query($staff_query);
    $staff = $staff_result->fetch_assoc();
    
    // Duration from form
    $duration = isset($data['duration']) ? (int)$data['duration'] : 4;
    
    // 1. Conflict Check (Revised for Robustness & No Break)
    // We check the current date, the day before, and the day after to catch all overlaps.
    $prev_date = date('Y-m-d', strtotime($data['date'] . ' -1 day'));
    $next_date = date('Y-m-d', strtotime($data['date'] . ' +1 day'));
    
    $check_stmt = $conn->prepare("SELECT booking_date, booking_time, duration FROM orders WHERE staff_id = ? AND booking_date IN (?, ?, ?) AND status NOT IN ('cancelled', 'completed')");
    $check_stmt->bind_param("isss", $staff_id, $data['date'], $prev_date, $next_date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    // Requested booking timestamps
    $req_start_ts = strtotime($data['date'] . ' ' . $data['start_time']);
    $req_duration_mins = $duration * 60; // Duration is in hours from form, convert to mins
    $req_end_ts = $req_start_ts + ($req_duration_mins * 60);
    
    while ($existing = $check_result->fetch_assoc()) {
        $ex_start_ts = strtotime($existing['booking_date'] . ' ' . $existing['booking_time']);
        $ex_duration_mins = $existing['duration'] ? $existing['duration'] : 60;
        $ex_end_ts = $ex_start_ts + ($ex_duration_mins * 60);
        
        // Conflict occurs if the time ranges overlap.
        // [req_start, req_end] overlaps with [ex_start, ex_end] if:
        if ($req_start_ts < $ex_end_ts && $req_end_ts > $ex_start_ts) {
            $conflict_start = date('Y-m-d H:i', $ex_start_ts);
            $conflict_end = date('Y-m-d H:i', $ex_end_ts);
            throw new Exception("the slot is already booked ($conflict_start to $conflict_end)");
        }
    }

    // 2. Create Order
    $order_code = 'ORD-' . strtoupper(substr(uniqid(), -5));
    // Use street_address for both pickup and delivery since we simplified the form
    $address = isset($data['street_address']) ? $data['street_address'] : (isset($data['pickup_address']) ? $data['pickup_address'] : '');
    $delivery_address = $address;
    $pickup_address = $address;
    
    // 0b. Fetch Service GST Rate securely
    $service_query_p = "SELECT gst_rate FROM services WHERE id = $service_id";
    $svc_res_p = $conn->query($service_query_p);
    $svc_data_p = $svc_res_p->fetch_assoc();
    $gst_rate = isset($svc_data_p['gst_rate']) ? (float)$svc_data_p['gst_rate'] : 10.00;

    // Recalculate total price securely
    $hourly_rate = $staff['daily_rate'] / 8;
    $subtotal = $hourly_rate * $duration; // Duration in hours
    $tax_amount = $subtotal * ($gst_rate / 100);
    $total_price = $subtotal + $tax_amount;
    
    // Store duration in MINUTES
    $duration_minutes = $duration * 60;

    $stmt = $conn->prepare("INSERT INTO orders (order_code, customer_name, customer_email, customer_phone, pickup_address, delivery_address, special_instructions, total_price, tax_amount, booking_date, booking_time, staff_id, city_id, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $booking_time = $data['start_time'] . ':00';
    
    $stmt->bind_param("sssssssddssiii", 
        $order_code, 
        $data['customer_name'], 
        $data['customer_email'], 
        $data['customer_phone'], 
        $pickup_address, 
        $delivery_address, 
        $data['special_instructions'],
        $total_price,
        $tax_amount,
        $data['date'],
        $booking_time,
        $data['staff_id'],
        $data['city_id'],
        $duration_minutes
    );
    
    $stmt->execute();
    $order_id = $conn->insert_id;

    // 2. Create Order Items (Single Item)
    // 2. Create Order Items (Single Item - Time Based)
    $svc_stmt = $conn->prepare("INSERT INTO order_items (order_id, service_id, quantity, price_at_booking) VALUES (?, ?, ?, ?)");
    $q_one = 1;
    $svc_stmt->bind_param("iidd", $order_id, $service_id, $q_one, $subtotal);
    $svc_stmt->execute();

    $conn->commit();
    
    // Trigger automation workflows for "order_booked" event
    require_once '../includes/automation.php';
    run_automation('order_booked', $order_id);

    // Success View
    require_once '../includes/header.php';
    ?>
    <div class="container py-5 text-center">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="antigravity-card p-5 animate__animated animate__zoomIn">
                    <div class="mb-4 text-primary">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-3">Booking Created!</h2>
                    <p class="text-muted mb-4">Redirecting you to payment...</p>
                    
                    <script>
                        setTimeout(function() {
                            window.location.href = 'booking_complete.php?order_id=<?php echo $order_id; ?>';
                        }, 1500);
                    </script>
                </div>
            </div>
        </div>
    </div>
    <?php
    require_once '../includes/footer.php';

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['booking_error'] = $e->getMessage();
    
    // Extract context for redirect
    $city_id = isset($_POST['city_id']) ? (int)$_POST['city_id'] : 0;
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
    $staff_id = isset($_POST['staff_id']) ? (int)$_POST['staff_id'] : 0;
    
    session_write_close();
    redirect("schedule.php?city_id=$city_id&service_id=$service_id&staff_id=$staff_id");
}
?>
