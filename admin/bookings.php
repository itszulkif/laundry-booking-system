<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle booking deletion if needed (optional but good for management)
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>alert('Booking deleted successfully'); window.location.href='bookings.php';</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Mobile Bookings</h2>
    <div class="badge bg-primary rounded-pill px-3 py-2">
        <?php
        $count_res = $conn->query("SELECT COUNT(*) as total FROM bookings");
        $count_data = $count_res->fetch_assoc();
        echo $count_data['total'] . " Total Bookings";
        ?>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Client Name</th>
                        <th>Service info</th>
                        <th>Price</th>
                        <th>Service Date & Time</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT b.*, s.name as db_staff_name 
                              FROM bookings b 
                              LEFT JOIN staff s ON b.provider_id = s.id 
                              ORDER BY b.created_at DESC";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $status_class = '';
                            switch ($row['status']) {
                                case 'pending': $status_class = 'bg-warning-soft text-warning'; break;
                                case 'confirmed': $status_class = 'bg-primary-soft text-primary'; break;
                                case 'completed': $status_class = 'bg-success-soft text-success'; break;
                                case 'cancelled': $status_class = 'bg-danger-soft text-danger'; break;
                            }
                    ?>
                        <tr id="booking-row-<?php echo $row['id']; ?>">
                            <td class="ps-4">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['user_name']); ?></div>
                                <small class="text-muted">ID: <?php echo htmlspecialchars($row['user_id']); ?></small>
                            </td>
                            <td>
                                <div class="badge bg-info-soft text-info rounded-pill">
                                    <?php echo htmlspecialchars($row['service_type'] ?: 'General'); ?>
                                </div>
                                <?php if($row['provider_name']): ?>
                                    <div class="small text-dark mt-1"><strong>Staff:</strong> <?php echo htmlspecialchars($row['provider_name']); ?></div>
                                <?php elseif($row['db_staff_name']): ?>
                                    <div class="small text-dark mt-1"><strong>Staff:</strong> <?php echo htmlspecialchars($row['db_staff_name']); ?></div>
                                <?php else: ?>
                                    <div class="small text-muted mt-1">Staff: Not Specified</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">PKR <?php echo number_format($row['price'], 2); ?></span>
                            </td>
                            <td>
                                <div><i class="bi bi-calendar3 me-1"></i> <?php echo date('M d, Y', strtotime($row['booking_date'])); ?></div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i> <?php echo date('h:i A', strtotime($row['booking_time'])); ?></small>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;">
                                    <i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($row['location']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small text-truncate" style="max-width: 150px;">
                                    <?php echo htmlspecialchars($row['description'] ?: 'No description'); ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?php echo $status_class; ?> rounded-pill status-badge" id="status-<?php echo $row['id']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="updateStatus(<?php echo $row['id']; ?>, 'confirmed')"><i class="bi bi-check-circle me-2 text-primary"></i> Confirm</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="updateStatus(<?php echo $row['id']; ?>, 'completed')"><i class="bi bi-check2-all me-2 text-success"></i> Mark as Done</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="updateStatus(<?php echo $row['id']; ?>, 'cancelled')"><i class="bi bi-x-circle me-2 text-danger"></i> Cancel</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')"><i class="bi bi-trash me-2"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                No bookings found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function updateStatus(bookingId, newStatus) {
    const formData = new FormData();
    formData.append('id', bookingId);
    formData.append('status', newStatus);

    fetch('update_booking_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const badge = document.getElementById('status-' + bookingId);
            badge.innerText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            
            // Update classes
            badge.className = 'badge rounded-pill status-badge';
            switch (newStatus) {
                case 'pending': badge.classList.add('bg-warning-soft', 'text-warning'); break;
                case 'confirmed': badge.classList.add('bg-primary-soft', 'text-primary'); break;
                case 'completed': badge.classList.add('bg-success-soft', 'text-success'); break;
                case 'cancelled': badge.classList.add('bg-danger-soft', 'text-danger'); break;
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status.');
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
