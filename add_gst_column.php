<?php
require_once 'includes/db.php';

// Check if gst_rate column exists
$check_query = "SHOW COLUMNS FROM services LIKE 'gst_rate'";
$result = $conn->query($check_query);

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $sql = "ALTER TABLE services ADD COLUMN gst_rate DECIMAL(5,2) DEFAULT 10.00 AFTER price";
    if ($conn->query($sql) === TRUE) {
        echo "Successfully added 'gst_rate' column to 'services' table.";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "'gst_rate' column already exists.";
}

$conn->close();
?>
