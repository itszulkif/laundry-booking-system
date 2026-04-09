<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['city_id']) || !isset($_GET['date'])) {
    echo json_encode([]);
    exit;
}

$city_id = (int)$_GET['city_id'];
$date = $_GET['date'];

// Fetch all bookings for this staff on this date, the previous date, and the next date
$prev_date = date('Y-m-d', strtotime($date . ' -1 day'));
$next_date = date('Y-m-d', strtotime($date . ' +1 day'));
$query = "SELECT staff_id, booking_date, booking_time, duration 
          FROM orders 
          WHERE booking_date IN ('$date', '$prev_date', '$next_date') 
          AND status NOT IN ('cancelled', 'completed')";

$result = $conn->query($query);

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $row_date = $row['booking_date'];
    $start_time = date('H:i', strtotime($row['booking_time']));
    $duration = $row['duration'] ? (int)$row['duration'] : 60;
    
    $start_timestamp = strtotime("$row_date $start_time");
    $end_timestamp = $start_timestamp + ($duration * 60);
    $end_time = date('H:i', $end_timestamp);
    
    // Check if this booking overlaps with the requested date
    $requested_day_start = strtotime("$date 00:00:00");
    $requested_day_end = strtotime("$date 23:59:59");
    
    if ($start_timestamp <= $requested_day_end && $end_timestamp >= $requested_day_start) {
        $bookings[] = [
            'staff_id' => $row['staff_id'],
            'start_time' => ($row_date == $date) ? $start_time : '00:00', // Start from 00:00 if it began yesterday
            'end_time' => ($end_timestamp > $requested_day_end) ? '23:59' : $end_time, // Cap at end of day
            'raw_start' => date('Y-m-d H:i', $start_timestamp),
            'raw_end' => date('Y-m-d H:i', $end_timestamp)
        ];
    }
}

echo json_encode($bookings);
?>
