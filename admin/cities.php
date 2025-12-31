<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $name = sanitize($conn, $_POST['name']);
    
    $conn->begin_transaction();
    try {
        if (isset($_POST['city_id']) && !empty($_POST['city_id'])) {
            // Update
            $id = (int)$_POST['city_id'];
            $stmt = $conn->prepare("UPDATE cities SET name=? WHERE id=?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
            
            // Update Staff Assignment (Clear previous first)
            $conn->query("UPDATE staff SET city_id = NULL WHERE city_id = $id");
            $city_id = $id;
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO cities (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $city_id = $conn->insert_id;
        }
        

        
        $conn->commit();
        echo "<script>window.location.href='cities.php';</script>";
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM cities WHERE id=$id");
    echo "<script>window.location.href='cities.php';</script>";
}

// Fetch Cities with Assigned Staff
// Fetch Cities
$cities_query = "SELECT * FROM cities ORDER BY name ASC";
$cities_result = $conn->query($cities_query);


?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">City Management</h1>
    <button type="button" class="btn btn-primary-soft" data-bs-toggle="modal" data-bs-target="#cityModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-2"></i> Add New City
    </button>
</div>

<div class="antigravity-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 rounded-start">City Name</th>

                    <th class="border-0 rounded-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($cities_result->num_rows > 0): ?>
                    <?php while($city = $cities_result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($city['name']); ?></td>

                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" 
                                        onclick='editCity(<?php echo json_encode($city); ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="cities.php?delete=<?php echo $city['id']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center py-4 text-muted">No cities found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- City Modal -->
<div class="modal fade" id="cityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Add New City</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" id="cityForm">
                    <input type="hidden" name="city_id" id="cityId">
                    
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">City Name</label>
                        <input type="text" name="name" id="cityName" class="form-control form-control-clean" required>
                    </div>
                    

                    
                    <button type="submit" class="btn btn-primary-soft w-100">Save City</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('cityForm').reset();
    document.getElementById('cityId').value = '';
    document.getElementById('modalTitle').innerText = 'Add New City';

}

function editCity(city) {
    document.getElementById('cityId').value = city.id;
    document.getElementById('cityName').value = city.name;
    document.getElementById('modalTitle').innerText = 'Edit City';
    

    
    var modal = new bootstrap.Modal(document.getElementById('cityModal'));
    modal.show();
}
</script>

<?php require_once 'includes/footer.php'; ?>
