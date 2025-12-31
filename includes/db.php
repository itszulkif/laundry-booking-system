<?php
$host = 'localhost';
$db   = 'laundry_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // Using MySQLi as requested in prompts, but PDO is generally better. 
     // However, the prompt explicitly said "HTML & PHP/MySQLi Logic".
     // I will stick to MySQLi to strictly follow the "Technology Focus" in the prompt.
     
     $conn = new mysqli($host, $user, $pass, $db);

     if ($conn->connect_error) {
         throw new Exception("Connection failed: " . $conn->connect_error);
     }
     
     $conn->set_charset("utf8mb4");

} catch (\Exception $e) {
     die("Database Connection Error: " . $e->getMessage());
}
?>
