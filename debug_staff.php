<?php
require_once 'includes/db.php';

echo "<h2>Staff Debug Information</h2>";

// 1. All Staff
echo "<h3>All Staff Members:</h3>";
$all_staff = $conn->query("SELECT * FROM staff");
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Status</th></tr>";
while($s = $all_staff->fetch_assoc()) {
    echo "<tr><td>{$s['id']}</td><td>{$s['name']}</td><td>{$s['status']}</td></tr>";
}
echo "</table><br>";

// 2. City Assignments
echo "<h3>City Assignments (city_staff):</h3>";
$city_staff = $conn->query("SELECT cs.*, s.name as staff_name, c.name as city_name FROM city_staff cs JOIN staff s ON cs.staff_id = s.id JOIN cities c ON cs.city_id = c.id");
echo "<table border='1'><tr><th>Staff Name</th><th>City Name</th></tr>";
while($cs = $city_staff->fetch_assoc()) {
    echo "<tr><td>{$cs['staff_name']}</td><td>{$cs['city_name']}</td></tr>";
}
echo "</table><br>";

// 3. Service Assignments
echo "<h3>Service Assignments (staff_services):</h3>";
$staff_services = $conn->query("SELECT ss.*, s.name as staff_name, svc.name as service_name FROM staff_services ss JOIN staff s ON ss.staff_id = s.id JOIN services svc ON ss.service_id = svc.id");
echo "<table border='1'><tr><th>Staff Name</th><th>Service Name</th></tr>";
while($ss = $staff_services->fetch_assoc()) {
    echo "<tr><td>{$ss['staff_name']}</td><td>{$ss['service_name']}</td></tr>";
}
echo "</table><br>";

// 4. Test Query for a specific service and city
echo "<h3>Test Query (Service ID=1, City ID=1):</h3>";
$test_query = "SELECT s.* FROM staff s 
                JOIN city_staff cs ON s.id = cs.staff_id 
                WHERE cs.city_id = 1
                AND s.status = 'available'
                AND s.id IN (
                    SELECT staff_id FROM staff_services 
                    WHERE service_id = 1
                )";
$test_result = $conn->query($test_query);
echo "<p>Found " . $test_result->num_rows . " staff members</p>";
echo "<table border='1'><tr><th>ID</th><th>Name</th></tr>";
while($t = $test_result->fetch_assoc()) {
    echo "<tr><td>{$t['id']}</td><td>{$t['name']}</td></tr>";
}
echo "</table>";
?>
