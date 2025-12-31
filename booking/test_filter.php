<?php
require_once '../includes/db.php';

echo "Starting Staff Filter Test...\n";

// 1. Setup Data
// Create City A and City B
$rand = rand(1000, 9999);
$conn->query("INSERT INTO cities (name) VALUES ('City A $rand')");
$cityA = $conn->insert_id;
$conn->query("INSERT INTO cities (name) VALUES ('City B $rand')");
$cityB = $conn->insert_id;

// Create Service
$conn->query("INSERT INTO services (name, price) VALUES ('Test Service', 10.00)");
$serviceId = $conn->insert_id;

// Create Staff A and Staff B
$conn->query("INSERT INTO staff (name, email, status) VALUES ('Staff A', 'staffA@test.com', 'available')");
$staffA = $conn->insert_id;
$conn->query("INSERT INTO staff (name, email, status) VALUES ('Staff B', 'staffB@test.com', 'available')");
$staffB = $conn->insert_id;

// Assign cities using city_staff table
$conn->query("INSERT INTO city_staff (city_id, staff_id) VALUES ($cityA, $staffA)");
$conn->query("INSERT INTO city_staff (city_id, staff_id) VALUES ($cityB, $staffB)");

// Assign Service to both
$conn->query("INSERT INTO staff_services (staff_id, service_id) VALUES ($staffA, $serviceId)");
$conn->query("INSERT INTO staff_services (staff_id, service_id) VALUES ($staffB, $serviceId)");

echo "Setup Complete. City A: $cityA, City B: $cityB, Staff A: $staffA, Staff B: $staffB\n";

// 2. Test Query for City A
$city_id = $cityA;
$service_id = $serviceId;
// Replicate query from schedule.php
$staff_query = "SELECT s.* FROM staff s 
                JOIN city_staff cs ON s.id = cs.staff_id
                WHERE cs.city_id = $city_id 
                AND s.status = 'available'
                AND s.id IN (
                    SELECT staff_id FROM staff_services 
                    WHERE service_id = $service_id
                )";
$result = $conn->query($staff_query);

echo "Querying for City A...\n";
$foundA = false;
$foundB = false;
while ($row = $result->fetch_assoc()) {
    if ($row['id'] == $staffA) $foundA = true;
    if ($row['id'] == $staffB) $foundB = true;
}

if ($foundA && !$foundB) {
    echo "PASS: Only Staff A found for City A.\n";
} else {
    echo "FAIL: Unexpected results for City A (Found A: " . ($foundA ? 'Yes' : 'No') . ", Found B: " . ($foundB ? 'Yes' : 'No') . ")\n";
}

// 3. Test Query for City B
$city_id = $cityB;
$staff_query = "SELECT s.* FROM staff s 
                JOIN city_staff cs ON s.id = cs.staff_id
                WHERE cs.city_id = $city_id 
                AND s.status = 'available'
                AND s.id IN (
                    SELECT staff_id FROM staff_services 
                    WHERE service_id = $service_id
                )";
$result = $conn->query($staff_query);

echo "Querying for City B...\n";
$foundA = false;
$foundB = false;
while ($row = $result->fetch_assoc()) {
    if ($row['id'] == $staffA) $foundA = true;
    if ($row['id'] == $staffB) $foundB = true;
}

if ($foundB && !$foundA) {
    echo "PASS: Only Staff B found for City B.\n";
} else {
    echo "FAIL: Unexpected results for City B (Found A: " . ($foundA ? 'Yes' : 'No') . ", Found B: " . ($foundB ? 'Yes' : 'No') . ")\n";
}

// Cleanup
$conn->query("DELETE FROM cities WHERE id IN ($cityA, $cityB)");
$conn->query("DELETE FROM staff WHERE id IN ($staffA, $staffB)");
$conn->query("DELETE FROM services WHERE id = $serviceId");
echo "Cleanup complete.\n";
?>
