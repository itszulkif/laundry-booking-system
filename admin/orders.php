<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle Status Update
// Handle Status Update
if (isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $conn->query("UPDATE orders SET status='$new_status' WHERE id=$order_id");

    // Send email if status is completed
    if ($new_status === 'completed') {
        require_once '../includes/automation.php';
        run_automation('order_completed', $order_id);
    }

    // Send email if status is cancelled
    if ($new_status === 'cancelled') {
        require_once '../includes/automation.php';
        run_automation('order_cancelled', $order_id);
    }

    echo "<script>window.location.href='orders.php';</script>";
}

// Fetch Orders grouped by status
$statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
$orders_by_status = [];
foreach ($statuses as $status) {
    $orders_by_status[$status] = [];
}

// Fetch All Staff for Filter
$staff_list_result = $conn->query("SELECT id, name FROM staff ORDER BY name ASC");
$staff_list = [];
while ($s = $staff_list_result->fetch_assoc()) {
    $staff_list[] = $s;
}

// Get Filter Parameters
$search_staff = isset($_GET['search_staff']) ? (int)$_GET['search_staff'] : 0;

$search_order = isset($_GET['search_order']) ? sanitize($conn, $_GET['search_order']) : '';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC'; // Default to Newest First

// Build Query
$where_clauses = [];
if ($search_staff > 0) {
    $where_clauses[] = "o.staff_id = $search_staff";
}
if (!empty($search_order)) {
    $where_clauses[] = "o.order_code LIKE '%$search_order%'";
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

$orders_query = "SELECT o.*, s.name as staff_name, c.name as city_name, 
                 s.name as staff_name, c.name as city_name 
                 FROM orders o 
                 LEFT JOIN staff s ON o.staff_id = s.id 
                 LEFT JOIN cities c ON o.city_id = c.id 
                 $where_sql
                 ORDER BY o.booking_date $sort_order, o.booking_time $sort_order";
$orders_result = $conn->query($orders_query);

while ($order = $orders_result->fetch_assoc()) {
    $orders_by_status[$order['status']][] = $order;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">Order Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="listViewBtn" onclick="switchView('list')">
            <i class="bi bi-list-ul me-1"></i>List View
        </button>
        <button type="button" class="btn btn-sm btn-primary-soft active" id="kanbanViewBtn" onclick="switchView('kanban')">
            <i class="bi bi-kanban me-1"></i>Kanban View
        </button>
    </div>
</div>

<!-- Search and Filter -->
<div class="antigravity-card p-4 mb-4">
    <form method="GET" action="orders.php" class="row g-3">
        <div class="col-md-3">
            <label class="form-label small text-muted fw-bold">Filter by Staff</label>
            <select name="search_staff" class="form-select form-control-clean">
                <option value="0">All Staff</option>
                <?php foreach($staff_list as $staff): ?>
                    <option value="<?php echo $staff['id']; ?>" <?php echo $search_staff == $staff['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($staff['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted fw-bold">Search Order ID</label>
            <input type="text" name="search_order" class="form-control form-control-clean" placeholder="e.g. ORD-12345" value="<?php echo htmlspecialchars($search_order); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted fw-bold">Sort By</label>
            <select name="sort_order" class="form-select form-control-clean">
                <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>Newest First</option>
                <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>Oldest First</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary-soft flex-grow-1">
                <i class="bi bi-search me-2"></i>Search
            </button>
            <a href="orders.php" class="btn btn-light">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<!-- List View -->
<div id="listView" style="display: none;">
    <div class="antigravity-card p-4">
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
                    <?php 
                    $orders_result->data_seek(0);
                    while($order = $orders_result->fetch_assoc()): 
                    ?>
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
                            <td class="fw-bold">PKR <?php echo number_format($order['total_price'], 2); ?></td>
                            <td>
                                <?php 
                                $status_class = match($order['status']) {
                                    'pending' => 'bg-warning text-dark',
                                    'confirmed' => 'bg-info text-white',
                                    'in_progress' => 'bg-primary text-white',
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
                                <button class="btn btn-sm btn-light border" onclick='viewOrder(<?php echo json_encode($order); ?>)'>
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Kanban View -->
<div id="kanbanView">
<div class="row flex-nowrap overflow-auto pb-4" style="min-height: 80vh;">
    <?php foreach ($statuses as $status): ?>
        <div class="col-md-4 col-xl-3">
            <div class="card bg-light border-0 h-100 rounded-3">
                <div class="card-header bg-transparent border-0 fw-bold text-uppercase small py-3">
                    <?php echo str_replace('_', ' ', $status); ?> 
                    <span class="badge bg-white text-dark rounded-pill ms-2"><?php echo count($orders_by_status[$status]); ?></span>
                </div>
                <div class="card-body p-2 overflow-auto" style="max-height: 75vh;">
                    <?php foreach ($orders_by_status[$status] as $order): ?>
                        <div class="antigravity-card p-3 mb-2 cursor-pointer" onclick='viewOrder(<?php echo json_encode($order); ?>)'>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-light text-dark border"><?php echo $order['order_code']; ?></span>
                                <small class="text-muted"><?php echo date('M d', strtotime($order['booking_date'])); ?></small>
                            </div>
                            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($order['customer_name']); ?></h6>
                            <p class="text-muted small mb-2 text-truncate"><?php echo htmlspecialchars($order['city_name']); ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle text-primary d-flex align-items-center justify-content-center small" style="width: 24px; height: 24px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <small class="ms-2 text-muted"><?php echo htmlspecialchars(explode(' ', $order['staff_name'])[0]); ?></small>
                                </div>
                                <span class="fw-bold small">PKR <?php echo number_format($order['total_price'], 2); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($orders_by_status[$status])): ?>
                        <div class="text-center text-muted small py-4">No orders</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="fw-bold text-primary mb-0" id="modalOrderCode">ORD-12345</h3>
                                <small class="text-muted" id="modalDate">Oct 24, 2023 at 10:00 AM</small>
                            </div>
                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6" id="modalStatus">Pending</span>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Customer Info</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-1 fw-bold" id="modalCustomerName">John Doe</p>
                                <p class="mb-1" id="modalCustomerEmail">john@example.com</p>
                                <p class="mb-0" id="modalCustomerPhone">+1 234 567 890</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Addresses</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border p-3 rounded h-100">
                                        <small class="text-primary fw-bold d-block mb-2">Pickup</small>
                                        <p class="mb-0 small" id="modalPickup">123 Main St...</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border p-3 rounded h-100">
                                        <small class="text-success fw-bold d-block mb-2">Delivery</small>
                                        <p class="mb-0 small" id="modalDelivery">123 Main St...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Services Booked</h6>
                            <div id="modalServices" class="bg-light p-3 rounded">
                                <!-- Services will be loaded here -->
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Special Instructions</h6>
                            <p class="small text-muted fst-italic" id="modalInstructions">None</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4 border-start">
                        <form method="POST">
                            <input type="hidden" name="order_id" id="modalOrderId">
                            <input type="hidden" name="update_status" value="1">
                            
                            <div class="mb-4">
                                <label class="form-label small text-muted fw-bold">Update Status</label>
                                <select name="status" id="modalStatusSelect" class="form-select form-control-clean">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                                <button type="submit" class="btn btn-primary-soft w-100 mt-2">Update</button>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <h6 class="text-uppercase text-muted small fw-bold mb-2">Assigned Staff</h6>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="bi bi-person fs-5"></i>
                                </div>
                                <span class="fw-bold" id="modalStaff">Unassigned</span>
                            </div>
                        </div>
                        
                        <hr>
                        
                        
                        <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Subtotal</span>
                                    <span class="fw-bold small" id="modalSubtotal">PKR 0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">GST</span>
                                    <span class="fw-bold small" id="modalTax">PKR 0.00</span>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <h6 class="text-uppercase text-muted small fw-bold mb-2">Total Amount</h6>
                            <h3 class="fw-bold text-primary" id="modalPrice">PKR 0.00</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrder(order) {
    document.getElementById('modalOrderId').value = order.id;
    document.getElementById('modalOrderCode').innerText = order.order_code;
    
    // Format time - only start time
    const startTime = order.booking_time ? order.booking_time.substring(0, 5) : '';
    
    document.getElementById('modalDate').innerText = order.booking_date + ' at ' + startTime;
    document.getElementById('modalStatus').innerText = order.status.replace('_', ' ').toUpperCase();
    document.getElementById('modalCustomerName').innerText = order.customer_name;
    document.getElementById('modalCustomerEmail').innerText = order.customer_email;
    document.getElementById('modalCustomerPhone').innerText = order.customer_phone;
    document.getElementById('modalPickup').innerText = order.pickup_address;
    document.getElementById('modalDelivery').innerText = order.delivery_address;
    document.getElementById('modalInstructions').innerText = order.special_instructions || 'None';
    document.getElementById('modalStaff').innerText = order.staff_name || 'Unassigned';
    const tax = parseFloat(order.tax_amount || 0);
    const total = parseFloat(order.total_price);
    const subtotal = total - tax;

    document.getElementById('modalSubtotal').innerText = 'PKR ' + subtotal.toFixed(2);
    document.getElementById('modalTax').innerText = 'PKR ' + tax.toFixed(2);
    document.getElementById('modalPrice').innerText = 'PKR ' + total.toFixed(2);
    document.getElementById('modalStatusSelect').value = order.status;
    
    
    // Fetch and display services
    fetch('../api/get_order_services.php?order_id=' + order.id)
        .then(response => response.json())
        .then(services => {
            let servicesHtml = '';
            if (services.length > 0) {
                services.forEach(service => {
                    servicesHtml += `
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <strong>${service.service_name}</strong>
                                <small class="text-muted d-block">Quantity: ${service.quantity} × PKR ${parseFloat(service.price_at_booking).toFixed(2)}</small>
                            </div>
                            <span class="fw-bold">PKR ${(service.quantity * service.price_at_booking).toFixed(2)}</span>
                        </div>
                    `;
                });
            } else {
                servicesHtml = '<p class="text-muted small mb-0">No services found</p>';
            }
            document.getElementById('modalServices').innerHTML = servicesHtml;
        })
        .catch(err => {
            console.error('Error fetching services:', err);
            document.getElementById('modalServices').innerHTML = '<p class="text-danger small mb-0">Error loading services</p>';
        });
    
    // Update Badge Color
    const badge = document.getElementById('modalStatus');
    badge.className = 'badge px-3 py-2 rounded-pill fs-6';
    if(order.status === 'pending') badge.classList.add('bg-warning', 'text-dark');
    else if(order.status === 'confirmed') badge.classList.add('bg-info', 'text-white');
    else if(order.status === 'completed') badge.classList.add('bg-success', 'text-white');
    else if(order.status === 'cancelled') badge.classList.add('bg-danger', 'text-white');
    else badge.classList.add('bg-secondary', 'text-white');

    var modal = new bootstrap.Modal(document.getElementById('orderModal'));
    modal.show();
}
function switchView(view) {
    const listView = document.getElementById('listView');
    const kanbanView = document.getElementById('kanbanView');
    const listBtn = document.getElementById('listViewBtn');
    const kanbanBtn = document.getElementById('kanbanViewBtn');
    
    if (view === 'list') {
        listView.style.display = 'block';
        kanbanView.style.display = 'none';
        listBtn.classList.remove('btn-outline-secondary');
        listBtn.classList.add('btn-primary-soft', 'active');
        kanbanBtn.classList.remove('btn-primary-soft', 'active');
        kanbanBtn.classList.add('btn-outline-secondary');
    } else {
        listView.style.display = 'none';
        kanbanView.style.display = 'block';
        kanbanBtn.classList.remove('btn-outline-secondary');
        kanbanBtn.classList.add('btn-primary-soft', 'active');
        listBtn.classList.remove('btn-primary-soft', 'active');
        listBtn.classList.add('btn-outline-secondary');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
