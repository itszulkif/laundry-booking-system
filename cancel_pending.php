<?php
require_once 'includes/db.php';

$id = 55;
$sql = "UPDATE orders SET status = 'cancelled' WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Order #$id has been cancelled successfully. Slot should be free.";
} else {
    echo "Error updating record: " . $conn->error;
}
?>
