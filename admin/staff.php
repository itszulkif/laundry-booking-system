<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name']);
    $email = sanitize($conn, $_POST['email']);
    $phone = sanitize($conn, $_POST['phone']);
    $bio = sanitize($conn, $_POST['bio']);
    $city_ids = isset($_POST['city_ids']) ? $_POST['city_ids'] : [];
    $status = $_POST['status'];
    $working_start = $_POST['working_start'];
    $working_end = $_POST['working_end'];
    $working_days = isset($_POST['working_days']) ? implode(',', $_POST['working_days']) : '';
    $assigned_services = isset($_POST['service_ids']) ? $_POST['service_ids'] : [];
    
    $conn->begin_transaction();
    try {
        if (isset($_POST['staff_id']) && !empty($_POST['staff_id'])) {
            // Update
            $id = (int)$_POST['staff_id'];
            $stmt = $conn->prepare("UPDATE staff SET name=?, email=?, phone=?, bio=?, status=?, working_start=?, working_end=?, working_days=? WHERE id=?");
            $stmt->bind_param("ssssssssi", $name, $email, $phone, $bio, $status, $working_start, $working_end, $working_days, $id);
            $stmt->execute();
            
            // Update Services
            $conn->query("DELETE FROM staff_services WHERE staff_id=$id");
            $staff_id = $id;
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO staff (name, email, phone, bio, status, working_start, working_end, working_days) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $name, $email, $phone, $bio, $status, $working_start, $working_end, $working_days);
            $stmt->execute();
            $staff_id = $conn->insert_id;
        }
        
        // Update City Assignments
        $conn->query("DELETE FROM city_staff WHERE staff_id=$staff_id");
        if (!empty($city_ids)) {
            $city_stmt = $conn->prepare("INSERT INTO city_staff (city_id, staff_id) VALUES (?, ?)");
            foreach ($city_ids as $city_id) {
                $city_stmt->bind_param("ii", $city_id, $staff_id);
                $city_stmt->execute();
            }
        }
        
        // Insert Service Assignments
        if (!empty($assigned_services)) {
            $svc_stmt = $conn->prepare("INSERT INTO staff_services (staff_id, service_id) VALUES (?, ?)");
            foreach ($assigned_services as $svc_id) {
                $svc_stmt->bind_param("ii", $staff_id, $svc_id);
                $svc_stmt->execute();
            }
        }
        
        $conn->commit();
        echo "<script>window.location.href='staff.php';</script>";
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM staff WHERE id=$id");
    echo "<script>window.location.href='staff.php';</script>";
}

// Get search and filter parameters
$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$city_filter = isset($_GET['city_filter']) ? (int)$_GET['city_filter'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 40;
$offset = ($page - 1) * $per_page;

// Build WHERE clause
$where_clauses = [];
if (!empty($search)) {
    $where_clauses[] = "s.name LIKE '%$search%'";
}
if ($city_filter > 0) {
    $where_clauses[] = "cs.city_id = $city_filter";
}
$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Count total records for pagination
$count_query = "SELECT COUNT(DISTINCT s.id) as total 
                FROM staff s 
                LEFT JOIN city_staff cs ON s.id = cs.staff_id
                LEFT JOIN cities c ON cs.city_id = c.id
                $where_sql";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

// Fetch Staff with Services and Cities
$staff_query = "SELECT s.*, 
                GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as city_names,
                GROUP_CONCAT(DISTINCT c.id) as city_ids,
                GROUP_CONCAT(DISTINCT svc.name SEPARATOR ', ') as service_names, 
                GROUP_CONCAT(DISTINCT svc.id) as service_ids 
                FROM staff s 
                LEFT JOIN city_staff cs ON s.id = cs.staff_id
                LEFT JOIN cities c ON cs.city_id = c.id
                LEFT JOIN staff_services ss ON s.id = ss.staff_id 
                LEFT JOIN services svc ON ss.service_id = svc.id 
                $where_sql
                GROUP BY s.id 
                ORDER BY s.created_at DESC
                LIMIT $per_page OFFSET $offset";
$staff_result = $conn->query($staff_query);

// Fetch All Services for Dropdown
$all_services = $conn->query("SELECT id, name FROM services WHERE status='active' ORDER BY name ASC");
$services_list = [];
while($svc = $all_services->fetch_assoc()) {
    $services_list[] = $svc;
}

// Fetch All Cities for Dropdown
$all_cities = $conn->query("SELECT id, name FROM cities ORDER BY name ASC");
$cities_list = [];
while($city = $all_cities->fetch_assoc()) {
    $cities_list[] = $city;
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">Staff Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary-soft" data-bs-toggle="modal" data-bs-target="#staffModal" onclick="resetForm()">
            <i class="bi bi-plus-lg me-2"></i> Add New Staff
        </button>
    </div>
</div>

<!-- Search and Filter -->
<div class="antigravity-card p-4 mb-4">
    <form method="GET" action="staff.php" class="row g-3">
        <div class="col-md-5">
            <label class="form-label small text-muted fw-bold">Search by Name</label>
            <input type="text" name="search" class="form-control form-control-clean" placeholder="Enter staff name..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted fw-bold">Filter by City</label>
            <select name="city_filter" class="form-select form-control-clean">
                <option value="0">All Cities</option>
                <?php foreach($cities_list as $city): ?>
                    <option value="<?php echo $city['id']; ?>" <?php echo $city_filter == $city['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($city['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary-soft flex-grow-1">
                <i class="bi bi-search me-2"></i>Search
            </button>
            <a href="staff.php" class="btn btn-light">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<div class="antigravity-card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 rounded-start">Staff Member</th>
                    <th class="border-0">City</th>
                    <th class="border-0">Assigned Services</th>
                    <th class="border-0">Schedule</th>
                    <th class="border-0">Status</th>
                    <th class="border-0 rounded-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($staff_result->num_rows > 0): ?>
                    <?php while($staff = $staff_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="../assets/img/<?php echo $staff['avatar']; ?>" 
                                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($staff['name']); ?>&background=random'"
                                         class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                                    <div>
                                        <div class="fw-bold"><?php echo htmlspecialchars($staff['name']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars(substr($staff['bio'], 0, 30)) . '...'; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if($staff['city_names']): ?>
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 200px;">
                                        <?php foreach(explode(', ', $staff['city_names']) as $cname): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info"><?php echo htmlspecialchars($cname); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">Not Assigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($staff['service_names']): ?>
                                    <div class="d-flex flex-wrap gap-1" style="max-width: 200px;">
                                        <?php foreach(explode(', ', $staff['service_names']) as $sname): ?>
                                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($sname); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">No services assigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small fw-bold">
                                    <?php echo date('h:i A', strtotime($staff['working_start'])); ?> - 
                                    <?php echo date('h:i A', strtotime($staff['working_end'])); ?>
                                </div>
                                <div class="small text-muted text-truncate" style="max-width: 150px;">
                                    <?php echo htmlspecialchars($staff['working_days']); ?>
                                </div>
                            </td>
                            <td>
                                <?php if($staff['status'] == 'available'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Available</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Busy</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" 
                                        onclick='editStaff(<?php echo json_encode($staff); ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="staff.php?delete=<?php echo $staff['id']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No staff members found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_records > $per_page): ?>
        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
            <div class="text-muted small">
                Showing <?php echo min($offset + 1, $total_records); ?> to <?php echo min($offset + $per_page, $total_records); ?> of <?php echo $total_records; ?> staff members
            </div>
            <nav aria-label="Staff pagination">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Previous Button -->
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&city_filter=<?php echo $city_filter; ?>&page=<?php echo $page - 1; ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php
                    // Show max 5 page numbers
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search) . '&city_filter=' . $city_filter . '&page=1">1</a></li>';
                        if ($start_page > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $active = $i == $page ? 'active' : '';
                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="?search=' . urlencode($search) . '&city_filter=' . $city_filter . '&page=' . $i . '">' . $i . '</a></li>';
                    }
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?search=' . urlencode($search) . '&city_filter=' . $city_filter . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
                    }
                    ?>
                    
                    <!-- Next Button -->
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?search=<?php echo urlencode($search); ?>&city_filter=<?php echo $city_filter; ?>&page=<?php echo $page + 1; ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Staff Modal -->
<div class="modal fade" id="staffModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Add New Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" id="staffForm">
                    <input type="hidden" name="staff_id" id="staffId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Full Name</label>
                                <input type="text" name="name" id="staffName" class="form-control form-control-clean" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Status</label>
                                <select name="status" id="staffStatus" class="form-select form-control-clean">
                                    <option value="available">Available</option>
                                    <option value="busy">Busy</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Email</label>
                            <input type="email" name="email" id="staffEmail" class="form-control form-control-clean" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Phone</label>
                            <input type="text" name="phone" id="staffPhone" class="form-control form-control-clean">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Bio</label>
                        <textarea name="bio" id="staffBio" class="form-control form-control-clean" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Assigned Cities</label>
                        <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                            <?php foreach($cities_list as $city): ?>
                                <div class="form-check">
                                    <input class="form-check-input city-checkbox" type="checkbox" name="city_ids[]" 
                                           value="<?php echo $city['id']; ?>" id="city_<?php echo $city['id']; ?>">
                                    <label class="form-check-label" for="city_<?php echo $city['id']; ?>">
                                        <?php echo htmlspecialchars($city['name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text small">Select cities where this staff member can work.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Assigned Services</label>
                        <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                            <?php foreach($services_list as $svc): ?>
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox" type="checkbox" name="service_ids[]" 
                                           value="<?php echo $svc['id']; ?>" id="svc_<?php echo $svc['id']; ?>">
                                    <label class="form-check-label" for="svc_<?php echo $svc['id']; ?>">
                                        <?php echo htmlspecialchars($svc['name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text small">Select services this staff member can perform.</div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Working Schedule</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Start Time</label>
                            <input type="time" name="working_start" id="staffStart" class="form-control form-control-clean" value="09:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">End Time</label>
                            <input type="time" name="working_end" id="staffEnd" class="form-control form-control-clean" value="17:00" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold d-block">Working Days</label>
                        <div class="btn-group" role="group">
                            <?php 
                            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                            foreach($days as $day): 
                            ?>
                            <input type="checkbox" class="btn-check" name="working_days[]" id="day_<?php echo $day; ?>" value="<?php echo $day; ?>" checked>
                            <label class="btn btn-outline-primary" for="day_<?php echo $day; ?>"><?php echo $day; ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-soft w-100">Save Staff Member</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('staffForm').reset();
    document.getElementById('staffId').value = '';
    document.getElementById('modalTitle').innerText = 'Add New Staff';
    // Reset days
    document.querySelectorAll('input[name="working_days[]"]').forEach(cb => cb.checked = true);
    // Reset services
    document.querySelectorAll('.service-checkbox').forEach(cb => cb.checked = false);
    // Reset cities
    document.querySelectorAll('.city-checkbox').forEach(cb => cb.checked = false);
}

function editStaff(staff) {
    document.getElementById('staffId').value = staff.id;
    document.getElementById('staffName').value = staff.name;
    document.getElementById('staffEmail').value = staff.email;
    document.getElementById('staffPhone').value = staff.phone;
    document.getElementById('staffBio').value = staff.bio;
    document.getElementById('staffStatus').value = staff.status;
    document.getElementById('staffStart').value = staff.working_start;
    document.getElementById('staffEnd').value = staff.working_end;
    document.getElementById('modalTitle').innerText = 'Edit Staff';
    
    // Handle Days
    document.querySelectorAll('input[name="working_days[]"]').forEach(cb => cb.checked = false);
    if (staff.working_days) {
        const days = staff.working_days.split(',');
        days.forEach(day => {
            const cb = document.getElementById('day_' + day);
            if(cb) cb.checked = true;
        });
    }

    // Handle Cities
    document.querySelectorAll('.city-checkbox').forEach(cb => cb.checked = false);
    if (staff.city_ids) {
        const ids = staff.city_ids.split(',');
        ids.forEach(id => {
            const cb = document.getElementById('city_' + id);
            if(cb) cb.checked = true;
        });
    }

    // Handle Services
    document.querySelectorAll('.service-checkbox').forEach(cb => cb.checked = false);
    if (staff.service_ids) {
        const ids = staff.service_ids.split(',');
        ids.forEach(id => {
            const cb = document.getElementById('svc_' + id);
            if(cb) cb.checked = true;
        });
    }
    
    var modal = new bootstrap.Modal(document.getElementById('staffModal'));
    modal.show();
}
</script>

<?php require_once 'includes/footer.php'; ?>
