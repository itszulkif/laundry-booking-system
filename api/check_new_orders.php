<?php
require_once '../includes/db.php';

// Get the latest order ID
$last_order_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

// Fetch new orders since the last check
$query = "SELECT o.*, s.name as staff_name, c.name as city_name 
          FROM orders o 
          LEFT JOIN staff s ON o.staff_id = s.id 
          LEFT JOIN cities c ON o.city_id = c.id 
          WHERE o.id > $last_order_id 
          ORDER BY o.id ASC 
          LIMIT 10";

$result = $conn->query($query);

$new_orders = [];
while ($row = $result->fetch_assoc()) {
    $new_orders[] = [
        'id' => $row['id'],
        'order_code' => $row['order_code'],
        'customer_name' => $row['customer_name'],
        'total_price' => $row['total_price'],
        'status' => $row['status'],
        'booking_date' => $row['booking_date'],
        'booking_time' => $row['booking_time'],
        'city_name' => $row['city_name'],
        'created_at' => $row['created_at']
    ];
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'new_orders' => $new_orders,
    'count' => count($new_orders)
]);
?>
