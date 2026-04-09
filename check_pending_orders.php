<?php
require_once 'includes/db.php';

$query = "SELECT id, customer_name, booking_date, booking_time, status, created_at FROM orders WHERE status = 'pending' ORDER BY id DESC LIMIT 5";
$result = $conn->query($query);

echo "--- Recent Pending Orders ---\n";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | " . $row['booking_date'] . " " . $row['booking_time'] . " | Status: " . $row['status'] . " | Created: " . $row['created_at'] . "\n";
    }
} else {
    echo "No pending orders found.\n";
}
?>
