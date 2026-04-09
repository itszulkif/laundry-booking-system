<?php
require_once '../includes/db.php';

// Set header for JSON response
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Please use POST.']);
    exit;
}

// Get the JSON data from the request body
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// Check if JSON decoding was successful
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data.']);
    exit;
}

// Validate required fields
$required_fields = ['user_id', 'user_name', 'booking_date', 'booking_time', 'location'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        echo json_encode(['status' => 'error', 'message' => "Missing or empty required field: $field"]);
        exit;
    }
}

// Sanitize and extract data
$user_id = $data['user_id'];
$user_name = $data['user_name'];
$provider_id = isset($data['provider_id']) ? $data['provider_id'] : '';
$provider_name = isset($data['provider_name']) ? $data['provider_name'] : '';
$service_type = isset($data['service_type']) ? $data['service_type'] : 'General Service';
$price = isset($data['price']) ? (float)$data['price'] : 0.00;
$booking_date = $data['booking_date'];
$booking_time = $data['booking_time'];
$location = $data['location'];
$description = isset($data['description']) ? $data['description'] : '';

// Use MySQLi Prepared Statements for security
$stmt = $conn->prepare("INSERT INTO bookings (user_id, user_name, provider_id, provider_name, service_type, price, booking_date, booking_time, location, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssdssss", $user_id, $user_name, $provider_id, $provider_name, $service_type, $price, $booking_date, $booking_time, $location, $description);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
