<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Get search parameter
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

// Build WHERE clause
$where_sql = '';
if (!empty($search)) {
    $where_sql = "WHERE customer_name LIKE '%$search%' OR customer_email LIKE '%$search%' OR customer_phone LIKE '%$search%'";
}

// Fetch all unique customers from orders
$query = "SELECT 
            customer_name,
            customer_email,
            customer_phone
          FROM orders 
          $where_sql
          GROUP BY customer_email, customer_name, customer_phone
          ORDER BY customer_name ASC";

$result = $conn->query($query);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">Booking Users</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="export_users.php" class="btn btn-success" download>
            <i class="bi bi-file-earmark-excel me-2"></i> Export to Excel
        </a>
    </div>
</div>


<!-- Search Bar -->
<div class="antigravity-card p-4 mb-4">
    <form method="GET" action="users_list.php" class="row g-3">
        <div class="col-md-10">
            <label class="form-label small text-muted fw-bold">Search Users</label>
            <input type="text" name="search" class="form-control form-control-clean" 
                   placeholder="Search by name, email, or phone..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary-soft flex-grow-1">
                <i class="bi bi-search me-2"></i>Search
            </button>
            <?php if (!empty($search)): ?>
                <a href="users_list.php" class="btn btn-light">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="antigravity-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 rounded-start">Name</th>
                    <th class="border-0">Email</th>
                    <th class="border-0 rounded-end">Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3 text-center" style="width: 40px; height: 40px;">
                                        <span class="fw-bold text-primary">
                                            <?php echo strtoupper(substr($user['customer_name'], 0, 1)); ?>
                                        </span>
                                    </div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($user['customer_name']); ?></div>
                                </div>
                            </td>
                            <td>
                                <i class="bi bi-envelope me-2 text-muted"></i>
                                <?php echo htmlspecialchars($user['customer_email']); ?>
                            </td>
                            <td>
                                <i class="bi bi-telephone me-2 text-muted"></i>
                                <?php echo htmlspecialchars($user['customer_phone']); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            <?php if (!empty($search)): ?>
                                No users found matching "<?php echo htmlspecialchars($search); ?>"
                            <?php else: ?>
                                No booking users yet.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
