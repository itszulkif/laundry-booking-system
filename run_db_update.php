<?php
require_once 'includes/db.php';

$sql = file_get_contents('update_schema_payments.sql');

if (empty($sql)) {
    die("Error: SQL file is empty or not found.");
}

// Split by semicolon to handle multiple statements if necessary, 
// though mysqli->multi_query is better for this.
if ($conn->multi_query($sql)) {
    do {
        /* store first result set */
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Database updated successfully.";
} else {
    echo "Error updating database: " . $conn->error;
}
?>
