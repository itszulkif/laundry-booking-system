<?php
$host = 'localhost';
$user = 'u109448269_book';
$pass = '*1~5>cSuk5W';
$db_name = 'u109448269_booking';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
