<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php');
}

$data = $_POST;
$service_id = (int)$data['service_id'];
$quantity = (int)$data['quantity'];

try {
    $conn->begin_transaction();

    // 0. Server-Side Conflict Check with 30-minute break
    // Fetch all bookings for this staff on this date
    $check_stmt = $conn->prepare("SELECT booking_time FROM orders WHERE staff_id = ? AND booking_date = ? AND status != 'cancelled'");
    $check_stmt->bind_param("is", $data['staff_id'], $data['date']);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    $requested_start = strtotime($data['start_time']);
    $requested_end = $requested_start + (30 * 60); // Add 30 minutes for our booking + break
    
    while ($existing = $check_result->fetch_assoc()) {
        $existing_start = strtotime($existing['booking_time']);
        $existing_end = $existing_start + (30 * 60); // Add 30 minutes for existing booking + break
        
        // Check if time ranges overlap
        if ($requested_start < $existing_end && $requested_end > $existing_start) {
            throw new Exception("This time slot conflicts with an existing booking or mandatory break period. Please choose a different time.");
        }
    }

    // 1. Create Order
    $order_code = 'ORD-' . strtoupper(substr(uniqid(), -5));
    $delivery_address = !empty($data['delivery_address']) ? $data['delivery_address'] : $data['pickup_address'];
    
    // Recalculate total price securely
    $price_query = "SELECT id, price FROM services WHERE id = $service_id";
    $price_result = $conn->query($price_query);
    $service = $price_result->fetch_assoc();
    
    $subtotal = $service['price'] * $quantity;
    $tax_amount = $subtotal * 0.10;
    $total_price = $subtotal + $tax_amount;

    $stmt = $conn->prepare("INSERT INTO orders (order_code, customer_name, customer_email, customer_phone, pickup_address, delivery_address, special_instructions, total_price, tax_amount, booking_date, booking_time, staff_id, city_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $booking_time = $data['start_time'] . ':00';
    
    $stmt->bind_param("sssssssddssii", 
        $order_code, 
        $data['customer_name'], 
        $data['customer_email'], 
        $data['customer_phone'], 
        $data['pickup_address'], 
        $delivery_address, 
        $data['special_instructions'],
        $total_price,
        $tax_amount,
        $data['date'],
        $booking_time,
        $data['staff_id'],
        $data['city_id']
    );
    
    $stmt->execute();
    $order_id = $conn->insert_id;

    // 2. Create Order Items (Single Item)
    $svc_stmt = $conn->prepare("INSERT INTO order_items (order_id, service_id, quantity, price_at_booking) VALUES (?, ?, ?, ?)");
    $svc_stmt->bind_param("iidd", $order_id, $service_id, $quantity, $service['price']);
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
                            window.location.href = 'payment.php?order_id=<?php echo $order_id; ?>';
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
    die("Error processing booking: " . $e->getMessage());
}
?>
