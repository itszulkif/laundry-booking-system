<?php
require_once 'includes/db.php';

echo "<h2>Setup Settings Table</h2>";

// Create Table
$sql = "CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'settings' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Default Values
$defaults = [
    'smtp_host' => 'smtp.hostinger.com',
    'smtp_port' => '465',
    'smtp_username' => 'smtp@zouetech.co.uk',
    'smtp_password' => 'Admin#$@1',
    'smtp_encryption' => 'ssl',
    'from_email' => 'smtp@zouetech.co.uk',
    'from_name' => 'DR.SPIN'
];

foreach ($defaults as $key => $value) {
    $check = $conn->query("SELECT id FROM settings WHERE setting_key = '$key'");
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->bind_param("ss", $key, $value);
        if ($stmt->execute()) {
            echo "Inserted default for $key.<br>";
        } else {
            echo "Error inserting $key: " . $stmt->error . "<br>";
        }
    } else {
        echo "Setting $key already exists.<br>";
    }
}

echo "<br>Done! You can now delete this file.";
?>
