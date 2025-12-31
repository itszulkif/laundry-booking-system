<?php
require_once '../includes/db.php';
$result = $conn->query("DESCRIBE services");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>
