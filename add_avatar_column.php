<?php
require_once 'includes/db.php';

// Add avatar column if it doesn't exist
$check = $conn->query("SHOW COLUMNS FROM staff LIKE 'avatar'");
if ($check->num_rows == 0) {
    echo "Adding avatar column...<br>";
    $sql = "ALTER TABLE staff ADD COLUMN avatar VARCHAR(255) DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "Avatar column added successfully.<br>";
    } else {
        echo "Error adding avatar column: " . $conn->error . "<br>";
    }
} else {
    echo "Avatar column already exists.<br>";
}

echo "Done.";
?>
