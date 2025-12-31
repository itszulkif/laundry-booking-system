<?php
require_once '../includes/db.php';

function describeTable($conn, $table) {
    echo "Table: $table\n";
    $result = $conn->query("DESCRIBE $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . "\n";
        }
    } else {
        echo "Table not found or error: " . $conn->error . "\n";
    }
    echo "\n";
}

describeTable($conn, 'staff');
describeTable($conn, 'cities');
?>
