<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Fetch Metrics
// 1. Total Orders
$orders_query = "SELECT COUNT(*) as count FROM orders";
$total_orders = $conn->query($orders_query)->fetch_assoc()['count'];

// 2. Total Revenue
$revenue_query = "SELECT SUM(total_price) as total FROM orders WHERE status != 'cancelled'";
$total_revenue = $conn->query($revenue_query)->fetch_assoc()['total'] ?? 0;

// 3. Pending Orders
$pending_query = "SELECT COUNT(*) as count FROM orders WHERE status = 'pending'";
$pending_orders = $conn->query($pending_query)->fetch_assoc()['count'];

// 4. Active Staff
$staff_query = "SELECT COUNT(*) as count FROM staff WHERE status = 'available'";
$active_staff = $conn->query($staff_query)->fetch_assoc()['count'];

// Fetch Recent Orders
$recent_query = "SELECT o.*, s.name as staff_name, c.name as city_name 
                 FROM orders o 
                 LEFT JOIN staff s ON o.staff_id = s.id 
                 LEFT JOIN cities c ON o.city_id = c.id 
                 ORDER BY o.created_at DESC LIMIT 5";
$recent_result = $conn->query($recent_query);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="export_orders.php" class="btn btn-sm btn-outline-secondary">Export</a>
        </div>
    </div>
</div>

<!-- Metrics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="antigravity-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Revenue</h6>
                <div class="bg-success bg-opacity-10 p-2 rounded-circle text-success">
                    <i class="bi bi-currency-dollar"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0">$<?php echo number_format($total_revenue, 2); ?></h3>
            <small class="text-success"><i class="bi bi-arrow-up-short"></i> +12% from last month</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="antigravity-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Total Orders</h6>
                <div class="bg-primary bg-opacity-10 p-2 rounded-circle text-primary">
                    <i class="bi bi-bag-check"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0"><?php echo $total_orders; ?></h3>
            <small class="text-muted">Lifetime orders</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="antigravity-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Pending</h6>
                <div class="bg-warning bg-opacity-10 p-2 rounded-circle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0"><?php echo $pending_orders; ?></h3>
            <small class="text-danger">Needs attention</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="antigravity-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-muted text-uppercase small fw-bold mb-0">Active Staff</h6>
                <div class="bg-info bg-opacity-10 p-2 rounded-circle text-info">
                    <i class="bi bi-people"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-0"><?php echo $active_staff; ?></h3>
            <small class="text-muted">Currently available</small>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="antigravity-card p-4">
    <h5 class="fw-bold mb-4">Recent Orders</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 rounded-start">Order ID</th>
                    <th class="border-0">Customer</th>
                    <th class="border-0">Service Info</th>
                    <th class="border-0">Amount</th>
                    <th class="border-0">Status</th>
                    <th class="border-0 rounded-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_result->num_rows > 0): ?>
                    <?php while($order = $recent_result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold text-primary"><?php echo $order['order_code']; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2 text-center" style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($order['customer_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold small"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($order['city_name']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <i class="bi bi-calendar me-1"></i> <?php echo date('M d', strtotime($order['booking_date'])); ?>
                                    <br>
                                    <i class="bi bi-clock me-1"></i> <?php echo date('h:i A', strtotime($order['booking_time'])); ?>
                                </div>
                            </td>
                            <td class="fw-bold">$<?php echo number_format($order['total_price'], 2); ?></td>
                            <td>
                                <?php 
                                $status_class = match($order['status']) {
                                    'pending' => 'bg-warning text-dark',
                                    'confirmed' => 'bg-info text-white',
                                    'completed' => 'bg-success text-white',
                                    'cancelled' => 'bg-danger text-white',
                                    default => 'bg-secondary text-white'
                                };
                                ?>
                                <span class="badge <?php echo $status_class; ?> rounded-pill fw-normal px-3 py-2">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                </span>
                            </td>
                            <td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="orders.php?search=<?php echo $order['order_code']; ?>"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="orders.php?delete=<?php echo $order['id']; ?>" onclick="return confirm('Are you sure?')"><i class="bi bi-trash me-2"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No recent orders found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
