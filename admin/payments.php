<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filters
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$status = isset($_GET['status']) ? sanitize($conn, $_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($conn, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($conn, $_GET['date_to']) : '';

// Build Query
$where = "WHERE 1=1";
if (!empty($search)) {
    $where .= " AND (p.transaction_id LIKE '%$search%' OR p.payer_id LIKE '%$search%' OR o.order_code LIKE '%$search%')";
}
if (!empty($status)) {
    $where .= " AND p.payment_status = '$status'";
}
if (!empty($date_from)) {
    $where .= " AND DATE(p.created_at) >= '$date_from'";
}
if (!empty($date_to)) {
    $where .= " AND DATE(p.created_at) <= '$date_to'";
}

// Count Total
$count_query = "SELECT COUNT(*) as total FROM payments p LEFT JOIN orders o ON p.order_id = o.id $where";
$total_result = $conn->query($count_query);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch Data
$query = "SELECT p.*, o.order_code, o.customer_name FROM payments p 
          LEFT JOIN orders o ON p.order_id = o.id 
          $where 
          ORDER BY p.created_at DESC 
          LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">Transactions</h2>
    <a href="analytics.php" class="btn btn-outline-primary"><i class="bi bi-graph-up"></i> View Analytics</a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search ID or Order Code" value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">All Statuses</option>
                    <option value="COMPLETED" <?php echo $status == 'COMPLETED' ? 'selected' : ''; ?>>Completed</option>
                    <option value="PENDING" <?php echo $status == 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                    <option value="FAILED" <?php echo $status == 'FAILED' ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                <a href="payments.php" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Transaction ID</th>
                        <th>Order Code</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4"><span class="font-monospace small"><?php echo htmlspecialchars($row['transaction_id']); ?></span></td>
                                <td><a href="orders.php?search=<?php echo htmlspecialchars($row['order_code']); ?>" class="text-decoration-none"><?php echo htmlspecialchars($row['order_code']); ?></a></td>
                                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                <td class="fw-bold">$<?php echo number_format($row['amount'], 2); ?></td>
                                <td>
                                    <?php 
                                    $status_class = 'bg-secondary';
                                    if ($row['payment_status'] == 'COMPLETED') $status_class = 'bg-success';
                                    elseif ($row['payment_status'] == 'PENDING') $status_class = 'bg-warning text-dark';
                                    elseif ($row['payment_status'] == 'FAILED') $status_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['payment_status']); ?></span>
                                </td>
                                <td class="text-muted small"><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
                                <td class="text-end pe-4">
                                    <a href="payment_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i> View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No transactions found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="card-footer bg-white border-0 py-3">
        <nav>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo $search; ?>&status=<?php echo $status; ?>">Previous</a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo $search; ?>&status=<?php echo $status; ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
