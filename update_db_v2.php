<?php
require_once 'includes/db.php';

$sql1 = "ALTER TABLE bookings ADD COLUMN provider_id VARCHAR(255) AFTER user_name";
$sql2 = "ALTER TABLE bookings ADD COLUMN service_type VARCHAR(255) AFTER provider_id";

if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
    echo "Columns 'provider_id' and 'service_type' added successfully.";
} else {
    echo "Error updating table: " . $conn->error;
}

$conn->close();
?>
