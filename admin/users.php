<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Get current logged-in admin ID
$current_admin_id = $_SESSION['admin_id'] ?? 0;

// Handle Form Submission (Create/Edit Admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_admin'])) {
    $name = sanitize($conn, $_POST['name']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $username = sanitize($conn, $_POST['username']);
    $password = $_POST['password'];
    
    if (isset($_POST['admin_id']) && !empty($_POST['admin_id'])) {
        // Update existing admin
        $admin_id = (int)$_POST['admin_id'];
        
        if (!empty($password)) {
            // Update with new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, phone=?, username=?, password=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $email, $phone, $username, $hashed_password, $admin_id);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE admins SET name=?, email=?, phone=?, username=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $username, $admin_id);
        }
        $stmt->execute();
    } else {
        // Create new admin
        if (empty($password)) {
            $error = "Password is required for new admin";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (name, email, phone, username, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $phone, $username, $hashed_password);
            $stmt->execute();
        }
    }
    
    if (!isset($error)) {
        echo "<script>window.location.href='users.php';</script>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prevent deleting own account
    if ($id !== $current_admin_id) {
        $conn->query("DELETE FROM admins WHERE id=$id");
    }
    echo "<script>window.location.href='users.php';</script>";
}

// Fetch Admins
$admins_query = "SELECT * FROM admins ORDER BY created_at DESC";
$admins_result = $conn->query($admins_query);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">User Management</h1>
    <button type="button" class="btn btn-primary-soft" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="resetForm()">
        <i class="bi bi-plus-lg me-2"></i> Add Administrator
    </button>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Admins List -->
<div class="antigravity-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 rounded-start">Name</th>
                    <th class="border-0">Username</th>
                    <th class="border-0">Email</th>
                    <th class="border-0">Phone</th>
                    <th class="border-0">Created</th>
                    <th class="border-0 rounded-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($admins_result->num_rows > 0): ?>
                    <?php while($admin = $admins_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2 text-primary text-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($admin['name']); ?></div>
                                        <?php if ($admin['id'] == $current_admin_id): ?>
                                            <small class="badge bg-success">You</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($admin['username']); ?></td>
                            <td><?php echo htmlspecialchars($admin['email']); ?></td>
                            <td><?php echo htmlspecialchars($admin['phone']); ?></td>
                            <td class="small text-muted"><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick='editAdmin(<?php echo json_encode($admin); ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php if ($admin['id'] !== $current_admin_id): ?>
                                    <a href="users.php?delete=<?php echo $admin['id']; ?>" 
                                       class="btn btn-sm btn-light text-danger" 
                                       onclick="return confirm('Are you sure you want to delete this administrator?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No administrators found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Add Administrator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" id="adminForm">
                    <input type="hidden" name="admin_id" id="adminId">
                    <input type="hidden" name="save_admin" value="1">
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Name</label>
                        <input type="text" name="name" id="adminName" class="form-control form-control-clean" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Email</label>
                        <input type="email" name="email" id="adminEmail" class="form-control form-control-clean">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Phone</label>
                        <input type="tel" name="phone" id="adminPhone" class="form-control form-control-clean">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Username</label>
                        <input type="text" name="username" id="adminUsername" class="form-control form-control-clean" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Password</label>
                        <input type="password" name="password" id="adminPassword" class="form-control form-control-clean">
                        <small class="text-muted" id="passwordHelp">Leave blank to keep current password</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-soft w-100">Save Administrator</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('adminForm').reset();
    document.getElementById('adminId').value = '';
    document.getElementById('modalTitle').innerText = 'Add Administrator';
    document.getElementById('passwordHelp').innerText = 'Required for new administrator';
    document.getElementById('adminPassword').required = true;
}

function editAdmin(admin) {
    document.getElementById('adminId').value = admin.id;
    document.getElementById('adminName').value = admin.name;
    document.getElementById('adminEmail').value = admin.email;
    document.getElementById('adminPhone').value = admin.phone;
    document.getElementById('adminUsername').value = admin.username;
    document.getElementById('adminPassword').value = '';
    document.getElementById('modalTitle').innerText = 'Edit Administrator';
    document.getElementById('passwordHelp').innerText = 'Leave blank to keep current password';
    document.getElementById('adminPassword').required = false;
    
    var modal = new bootstrap.Modal(document.getElementById('adminModal'));
    modal.show();
}
</script>

<?php require_once 'includes/footer.php'; ?>
