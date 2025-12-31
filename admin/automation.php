<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle Form Submission (Create/Edit Workflow)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_workflow'])) {
    $workflow_name = sanitize($conn, $_POST['workflow_name']);
    $status = $_POST['status'];
    $event_type = $_POST['event_type'];
    $action_type = $_POST['action_type'];
    $to_email = $_POST['to_email'];
    $email_subject = sanitize($conn, $_POST['email_subject']);
    $email_content = $_POST['email_content']; // Don't sanitize HTML content
    
    $conn->begin_transaction();
    try {
        if (isset($_POST['workflow_id']) && !empty($_POST['workflow_id'])) {
            // Update existing workflow
            $workflow_id = (int)$_POST['workflow_id'];
            $stmt = $conn->prepare("UPDATE workflows SET workflow_name=?, status=?, event_type=? WHERE id=?");
            $stmt->bind_param("sssi", $workflow_name, $status, $event_type, $workflow_id);
            $stmt->execute();
            
            // Update action
            $action_result = $conn->query("SELECT id FROM workflow_actions WHERE workflow_id=$workflow_id LIMIT 1");
            if ($action_result->num_rows > 0) {
                $action_id = $action_result->fetch_assoc()['id'];
                $stmt = $conn->prepare("UPDATE workflow_emails SET to_email_field=?, email_subject=?, email_content=? WHERE action_id=?");
                $stmt->bind_param("sssi", $to_email, $email_subject, $email_content, $action_id);
                $stmt->execute();
            }
        } else {
            // Create new workflow
            $stmt = $conn->prepare("INSERT INTO workflows (workflow_name, status, event_type) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $workflow_name, $status, $event_type);
            $stmt->execute();
            $workflow_id = $conn->insert_id;
            
            // Create action
            $stmt = $conn->prepare("INSERT INTO workflow_actions (workflow_id, action_type) VALUES (?, ?)");
            $stmt->bind_param("is", $workflow_id, $action_type);
            $stmt->execute();
            $action_id = $conn->insert_id;
            
            // Create email configuration
            $stmt = $conn->prepare("INSERT INTO workflow_emails (action_id, to_email_field, email_subject, email_content) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $action_id, $to_email, $email_subject, $email_content);
            $stmt->execute();
        }
        
        $conn->commit();
        echo "<script>window.location.href='automation.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error: " . $e->getMessage();
    }
}

// Handle Status Toggle
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    $conn->query("UPDATE workflows SET status = IF(status='active', 'inactive', 'active') WHERE id=$id");
    echo "<script>window.location.href='automation.php';</script>";
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM workflows WHERE id=$id");
    echo "<script>window.location.href='automation.php';</script>";
}

// Handle Delete All Logs
if (isset($_GET['delete_all_logs'])) {
    $conn->query("DELETE FROM workflow_activity_log");
    echo "<script>window.location.href='automation.php';</script>";
}

// Fetch Workflows
$workflows_query = "SELECT w.*, 
                    (SELECT COUNT(*) FROM workflow_activity_log WHERE workflow_id = w.id AND status='success') as success_count,
                    (SELECT COUNT(*) FROM workflow_activity_log WHERE workflow_id = w.id AND status='failed') as failed_count
                    FROM workflows w 
                    ORDER BY w.created_at DESC";
$workflows_result = $conn->query($workflows_query);

// Fetch Activity Log
$activity_query = "SELECT al.*, w.workflow_name, o.order_code 
                   FROM workflow_activity_log al
                   LEFT JOIN workflows w ON al.workflow_id = w.id
                   LEFT JOIN orders o ON al.order_id = o.id
                   ORDER BY al.created_at DESC
                   LIMIT 50";
$activity_result = $conn->query($activity_query);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">Automation</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-primary-soft" data-bs-toggle="modal" data-bs-target="#workflowModal" onclick="resetForm()">
            <i class="bi bi-plus-lg me-2"></i> Create Workflow
        </button>
    </div>
</div>

<!-- Workflows Section -->
<div class="antigravity-card p-4 mb-4">
    <h5 class="fw-bold mb-4"><i class="bi bi-diagram-3 me-2"></i>Workflows</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="border-0 rounded-start">Workflow Name</th>
                    <th class="border-0">Event Type</th>
                    <th class="border-0">Status</th>
                    <th class="border-0">Executions</th>
                    <th class="border-0 rounded-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($workflows_result->num_rows > 0): ?>
                    <?php while($workflow = $workflows_result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($workflow['workflow_name']); ?></td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <?php echo ucfirst(str_replace('_', ' ', $workflow['event_type'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           <?php echo $workflow['status'] == 'active' ? 'checked' : ''; ?>
                                           onchange="window.location.href='automation.php?toggle_status=<?php echo $workflow['id']; ?>'">
                                    <label class="form-check-label small">
                                        <?php echo ucfirst($workflow['status']); ?>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success me-1"><?php echo $workflow['success_count']; ?> Success</span>
                                <span class="badge bg-danger"><?php echo $workflow['failed_count']; ?> Failed</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick='editWorkflow(<?php echo $workflow["id"]; ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="automation.php?delete=<?php echo $workflow['id']; ?>" 
                                   class="btn btn-sm btn-light text-danger" 
                                   onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No workflows found. Create your first workflow!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Activity Log Section -->
<div class="antigravity-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold m-0"><i class="bi bi-clock-history me-2"></i>Activity Log</h5>
        <a href="automation.php?delete_all_logs=1" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete all activity logs? This action cannot be undone.')">
            <i class="bi bi-trash me-1"></i> Delete All
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead class="bg-light">
                <tr>
                    <th class="border-0">Timestamp</th>
                    <th class="border-0">Workflow</th>
                    <th class="border-0">Order</th>
                    <th class="border-0">Action</th>
                    <th class="border-0">Status</th>
                    <th class="border-0">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($activity_result->num_rows > 0): ?>
                    <?php while($log = $activity_result->fetch_assoc()): ?>
                        <tr>
                            <td class="small"><?php echo date('M d, Y h:i A', strtotime($log['created_at'])); ?></td>
                            <td class="small"><?php echo htmlspecialchars($log['workflow_name']); ?></td>
                            <td class="small"><?php echo $log['order_code'] ?? 'N/A'; ?></td>
                            <td class="small"><?php echo ucfirst(str_replace('_', ' ', $log['action_type'])); ?></td>
                            <td>
                                <span class="badge <?php echo $log['status'] == 'success' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo ucfirst($log['status']); ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?php echo htmlspecialchars(substr($log['details'], 0, 50)); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No activity yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Workflow Modal -->
<div class="modal fade" id="workflowModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Create New Workflow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" id="workflowForm">
                    <input type="hidden" name="workflow_id" id="workflowId">
                    <input type="hidden" name="save_workflow" value="1">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label small text-muted fw-bold">Workflow Name</label>
                            <input type="text" name="workflow_name" id="workflowName" class="form-control form-control-clean" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Status</label>
                            <select name="status" id="workflowStatus" class="form-select form-control-clean">
                                <option value="inactive">Inactive</option>
                                <option value="active">Active</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Event Type</label>
                        <select name="event_type" id="eventType" class="form-select form-control-clean" required>
                            <option value="order_booked">Order Booked</option>
                        </select>
                        <small class="text-muted">Trigger this workflow when an order is placed</small>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Action Configuration</h6>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Action Type</label>
                        <select name="action_type" id="actionType" class="form-select form-control-clean" required>
                            <option value="send_email">Send Email</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">To Email</label>
                        <input type="text" name="to_email" id="toEmail" class="form-control form-control-clean" 
                               value="{{customer_email}}" required>
                        <small class="text-muted">Use {{customer_email}} for customer's email, {{admin_email}} for admin</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Email Subject</label>
                        <input type="text" name="email_subject" id="emailSubject" class="form-control form-control-clean" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold">Email Content</label>
                        <textarea name="email_content" id="emailContent" rows="10" class="form-control"></textarea>
                        <small class="text-muted">Available variables: {{customer_name}}, {{customer_email}}, {{order_code}}, {{booking_date}}, {{total_price}}</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary-soft w-100">Save Workflow</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/19mmwljpotr2rn5fw9580mapz0kvvp3n6jx4o7ycgtfg08s6/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#emailContent',
    height: 400,
    menubar: false,
    plugins: 'code link image lists',
    toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
});

function resetForm() {
    document.getElementById('workflowForm').reset();
    document.getElementById('workflowId').value = '';
    document.getElementById('modalTitle').innerText = 'Create New Workflow';
    tinymce.get('emailContent').setContent('');
}

function editWorkflow(id) {
    fetch('../api/get_workflow.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            document.getElementById('workflowId').value = data.id;
            document.getElementById('workflowName').value = data.workflow_name;
            document.getElementById('workflowStatus').value = data.status;
            document.getElementById('eventType').value = data.event_type;
            document.getElementById('actionType').value = data.action_type;
            document.getElementById('toEmail').value = data.to_email;
            document.getElementById('emailSubject').value = data.email_subject;
            tinymce.get('emailContent').setContent(data.email_content);
            document.getElementById('modalTitle').innerText = 'Edit Workflow';
            
            var modal = new bootstrap.Modal(document.getElementById('workflowModal'));
            modal.show();
        });
}
</script>

<?php require_once 'includes/footer.php'; ?>
