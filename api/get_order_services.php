<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['order_id'])) {
    echo json_encode([]);
    exit;
}

$order_id = (int)$_GET['order_id'];

// Fetch order items with service details
$query = "SELECT oi.*, s.name as service_name, s.price_unit 
          FROM order_items oi 
          JOIN services s ON oi.service_id = s.id 
          WHERE oi.order_id = $order_id";

$result = $conn->query($query);

$services = [];
while ($row = $result->fetch_assoc()) {
    $services[] = [
        'service_name' => $row['service_name'],
        'quantity' => $row['quantity'],
        'price_at_booking' => $row['price_at_booking'],
        'price_unit' => $row['price_unit']
    ];
}

echo json_encode($services);
?>
