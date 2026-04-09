<?php
require_once '../includes/db.php';

// Set header for JSON response
header('Content-Type: application/json');

// Get the base URL for images
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . '://' . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/api') . '/assets/img/';

// Fetch Staff with Cities and Services
$query = "SELECT s.id, s.name as staff_name, s.phone, s.avatar,
          GROUP_CONCAT(DISTINCT svc.name SEPARATOR ', ') as specialty
          FROM staff s 
          LEFT JOIN staff_services ss ON s.id = ss.staff_id 
          LEFT JOIN services svc ON ss.service_id = svc.id 
          GROUP BY s.id 
          ORDER BY s.name ASC";

$result = $conn->query($query);

$staff_list = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $staff_list[] = [
            'id' => (int)$row['id'],
            'staff_name' => $row['staff_name'],
            'specialty' => $row['specialty'] ?: 'General Service',
            'phone' => $row['phone'] ?: 'No phone provided',
            'image_url' => $base_url . $row['avatar']
        ];
    }
}

echo json_encode($staff_list);

$conn->close();
?>
