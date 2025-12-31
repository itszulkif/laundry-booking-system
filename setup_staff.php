<?php
require_once 'includes/db.php';

echo "<h2>Quick Setup: Assign Staff to Cities and Services</h2>";
echo "<p>This script will help you set up your first staff member with city and service assignments.</p>";

// Fetch all data
$staff = $conn->query("SELECT * FROM staff ORDER BY name");
$cities = $conn->query("SELECT * FROM cities ORDER BY name");
$services = $conn->query("SELECT * FROM services WHERE status='active' ORDER BY name");

if (isset($_POST['setup'])) {
    $staff_id = (int)$_POST['staff_id'];
    $city_ids = isset($_POST['city_ids']) ? $_POST['city_ids'] : [];
    $service_ids = isset($_POST['service_ids']) ? $_POST['service_ids'] : [];
    
    $conn->begin_transaction();
    try {
        // Clear existing assignments
        $conn->query("DELETE FROM city_staff WHERE staff_id = $staff_id");
        $conn->query("DELETE FROM staff_services WHERE staff_id = $staff_id");
        
        // Assign cities
        $stmt = $conn->prepare("INSERT INTO city_staff (city_id, staff_id) VALUES (?, ?)");
        foreach ($city_ids as $city_id) {
            $stmt->bind_param("ii", $city_id, $staff_id);
            $stmt->execute();
        }
        
        // Assign services
        $stmt = $conn->prepare("INSERT INTO staff_services (staff_id, service_id) VALUES (?, ?)");
        foreach ($service_ids as $service_id) {
            $stmt->bind_param("ii", $staff_id, $service_id);
            $stmt->execute();
        }
        
        $conn->commit();
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>✓ Success!</strong> Staff member has been assigned to " . count($city_ids) . " city/cities and " . count($service_ids) . " service(s).";
        echo "</div>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<strong>✗ Error:</strong> " . $e->getMessage();
        echo "</div>";
    }
}
?>

<form method="POST" style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
    <h3>Step 1: Select Staff Member</h3>
    <select name="staff_id" required style="width: 100%; padding: 10px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ccc;">
        <option value="">-- Choose Staff --</option>
        <?php 
        $staff->data_seek(0);
        while($s = $staff->fetch_assoc()): 
        ?>
            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?> (<?php echo $s['status']; ?>)</option>
        <?php endwhile; ?>
    </select>

    <h3>Step 2: Assign to Cities</h3>
    <div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px; max-height: 150px; overflow-y: auto;">
        <?php 
        $cities->data_seek(0);
        while($c = $cities->fetch_assoc()): 
        ?>
            <label style="display: block; padding: 5px;">
                <input type="checkbox" name="city_ids[]" value="<?php echo $c['id']; ?>">
                <?php echo htmlspecialchars($c['name']); ?>
            </label>
        <?php endwhile; ?>
    </div>

    <h3>Step 3: Assign to Services</h3>
    <div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 20px; max-height: 150px; overflow-y: auto;">
        <?php 
        $services->data_seek(0);
        while($svc = $services->fetch_assoc()): 
        ?>
            <label style="display: block; padding: 5px;">
                <input type="checkbox" name="service_ids[]" value="<?php echo $svc['id']; ?>">
                <?php echo htmlspecialchars($svc['name']); ?> ($<?php echo $svc['price']; ?>)
            </label>
        <?php endwhile; ?>
    </div>

    <button type="submit" name="setup" style="width: 100%; padding: 15px; background: #007bff; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
        Assign Staff Member
    </button>
</form>

<hr style="margin: 40px 0;">

<h3>Current Assignments</h3>
<?php
$assignments = $conn->query("
    SELECT 
        s.name as staff_name,
        GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as cities,
        GROUP_CONCAT(DISTINCT svc.name SEPARATOR ', ') as services
    FROM staff s
    LEFT JOIN city_staff cs ON s.id = cs.staff_id
    LEFT JOIN cities c ON cs.city_id = c.id
    LEFT JOIN staff_services ss ON s.id = ss.staff_id
    LEFT JOIN services svc ON ss.service_id = svc.id
    GROUP BY s.id
    ORDER BY s.name
");

echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f8f9fa;'><th>Staff Member</th><th>Assigned Cities</th><th>Assigned Services</th></tr>";
while($a = $assignments->fetch_assoc()) {
    $cities_display = $a['cities'] ?: '<em style="color: #999;">None</em>';
    $services_display = $a['services'] ?: '<em style="color: #999;">None</em>';
    echo "<tr>";
    echo "<td><strong>{$a['staff_name']}</strong></td>";
    echo "<td>{$cities_display}</td>";
    echo "<td>{$services_display}</td>";
    echo "</tr>";
}
echo "</table>";
?>

<p style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 5px;">
    <strong>Note:</strong> After assigning staff, go to <a href="index.php">the homepage</a> and try booking again. 
    Make sure to select a city and service that you've assigned to the staff member.
</p>
