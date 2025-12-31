<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $description = sanitize($conn, $_POST['description']);
    $price = (float)$_POST['price'];
    $icon = sanitize($conn, $_POST['icon']);
    $status = $_POST['status'];
    $price_unit = $_POST['price_unit'];
    
    if (isset($_POST['service_id']) && !empty($_POST['service_id'])) {
        // Update
        $id = (int)$_POST['service_id'];
        $stmt = $conn->prepare("UPDATE services SET name=?, description=?, price=?, icon=?, status=?, price_unit=? WHERE id=?");
        $stmt->bind_param("ssdsssi", $name, $description, $price, $icon, $status, $price_unit, $id);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO services (name, description, price, icon, status, price_unit) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsss", $name, $description, $price, $icon, $status, $price_unit);
    }
    
    if ($stmt->execute()) {
        echo "<script>window.location.href='services.php';</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM services WHERE id=$id");
    echo "<script>window.location.href='services.php';</script>";
}

// Fetch Services
$services_query = "SELECT * FROM services ORDER BY name ASC";
$services_result = $conn->query($services_query);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">Services Management</h1>
    <button type="button" class="btn btn-primary-soft" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-2"></i> Add New Service
    </button>
</div>

<div class="row g-4">
    <?php if ($services_result->num_rows > 0): ?>
        <?php while($service = $services_result->fetch_assoc()): ?>
            <div class="col-md-4 col-lg-3">
                <div class="antigravity-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-light rounded-circle p-3 text-primary">
                            <i class="bi <?php echo $service['icon']; ?> fs-4"></i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                <li><a class="dropdown-item" href="#" onclick='editService(<?php echo json_encode($service); ?>)'>Edit</a></li>
                                <li><a class="dropdown-item text-danger" href="services.php?delete=<?php echo $service['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($service['name']); ?></h5>
                    <p class="text-muted small mb-3 flex-grow-1"><?php echo htmlspecialchars($service['description']); ?></p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                        <div>
                            <span class="fw-bold fs-5">$<?php echo number_format($service['price'], 2); ?></span>
                            <span class="text-muted small">/ <?php echo $service['price_unit'] == 'kg' ? 'kg' : 'item'; ?></span>
                        </div>
                        <?php if($service['status'] == 'active'): ?>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Inactive</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <p class="text-muted">No services found. Add one to get started.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Service Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" id="serviceForm">
                    <input type="hidden" name="service_id" id="serviceId">
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Service Name</label>
                        <input type="text" name="name" id="serviceName" class="form-control form-control-clean" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Description</label>
                        <textarea name="description" id="serviceDesc" class="form-control form-control-clean" rows="2"></textarea>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Price ($)</label>
                            <input type="number" step="0.01" name="price" id="servicePrice" class="form-control form-control-clean" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Pricing Unit</label>
                            <select name="price_unit" id="serviceUnit" class="form-select form-control-clean">
                                <option value="item">Per Item</option>
                                <option value="kg">Per Kg</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Icon (Bootstrap Class)</label>
                        <input type="text" name="icon" id="serviceIcon" class="form-control form-control-clean" placeholder="bi-box-seam" required>
                        <div class="form-text small">e.g. bi-shirt, bi-basket</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Status</label>
                        <select name="status" id="serviceStatus" class="form-select form-control-clean">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-soft w-100">Save Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('serviceForm').reset();
    document.getElementById('serviceId').value = '';
    document.getElementById('modalTitle').innerText = 'Add New Service';
}

function editService(service) {
    document.getElementById('serviceId').value = service.id;
    document.getElementById('serviceName').value = service.name;
    document.getElementById('serviceDesc').value = service.description;
    document.getElementById('servicePrice').value = service.price;
    document.getElementById('serviceUnit').value = service.price_unit || 'item';
    document.getElementById('serviceIcon').value = service.icon;
    document.getElementById('serviceStatus').value = service.status;
    document.getElementById('modalTitle').innerText = 'Edit Service';
    
    var modal = new bootstrap.Modal(document.getElementById('serviceModal'));
    modal.show();
}
</script>

<?php require_once 'includes/footer.php'; ?>
