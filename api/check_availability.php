<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['city_id']) || !isset($_GET['date'])) {
    echo json_encode([]);
    exit;
}

$city_id = (int)$_GET['city_id'];
$date = $_GET['date'];

// Fetch all bookings for this city on this date (excluding cancelled)
$query = "SELECT o.staff_id, o.booking_time 
          FROM orders o 
          WHERE o.city_id = $city_id 
          AND o.booking_date = '$date' 
          AND o.status != 'cancelled'";

$result = $conn->query($query);

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $start_time = substr($row['booking_time'], 0, 5); // HH:MM format
    
    // Calculate end time: start time + 30 minutes (booking + break)
    $start_datetime = new DateTime($start_time);
    $start_datetime->modify('+30 minutes');
    $break_end_time = $start_datetime->format('H:i');
    
    $bookings[] = [
        'staff_id' => $row['staff_id'],
        'start_time' => $start_time,
        'break_end_time' => $break_end_time, // Time when staff becomes available again
        'unavailable_until' => $break_end_time // Staff unavailable until break ends
    ];
}

echo json_encode($bookings);
?>
