<?php
require_once '../includes/db.php';
require_once 'includes/header.php';

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'smtp_host' => $_POST['smtp_host'],
        'smtp_port' => $_POST['smtp_port'],
        'smtp_username' => $_POST['smtp_username'],
        'smtp_password' => $_POST['smtp_password'],
        'smtp_encryption' => $_POST['smtp_encryption'],
        'from_email' => $_POST['from_email'],
        'from_name' => $_POST['from_name']
    ];

    foreach ($settings as $key => $value) {
        $value = $conn->real_escape_string($value);
        // Insert or Update
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES ('$key', '$value') 
                ON DUPLICATE KEY UPDATE setting_value = '$value'";
        $conn->query($sql);
    }
    
    $success_msg = "Settings saved successfully!";
}

// Fetch Current Settings
$current_settings = [];
$result = $conn->query("SELECT * FROM settings");
while ($row = $result->fetch_assoc()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

// Helper to get setting safely
function get_setting($key, $settings) {
    return isset($settings[$key]) ? htmlspecialchars($settings[$key]) : '';
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold">System Settings</h1>
</div>

<?php if (isset($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?php echo $success_msg; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="antigravity-card p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-envelope-gear text-primary me-2"></i>SMTP Configuration</h5>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">SMTP Host</label>
                    <input type="text" name="smtp_host" class="form-control form-control-clean" 
                           value="<?php echo get_setting('smtp_host', $current_settings); ?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-bold">SMTP Port</label>
                        <input type="text" name="smtp_port" class="form-control form-control-clean" 
                               value="<?php echo get_setting('smtp_port', $current_settings); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-bold">Encryption</label>
                        <select name="smtp_encryption" class="form-select form-control-clean">
                            <option value="ssl" <?php echo get_setting('smtp_encryption', $current_settings) == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            <option value="tls" <?php echo get_setting('smtp_encryption', $current_settings) == 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="" <?php echo get_setting('smtp_encryption', $current_settings) == '' ? 'selected' : ''; ?>>None</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">SMTP Username</label>
                    <input type="text" name="smtp_username" class="form-control form-control-clean" 
                           value="<?php echo get_setting('smtp_username', $current_settings); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">SMTP Password</label>
                    <div class="input-group">
                        <input type="password" name="smtp_password" id="smtpPassword" class="form-control form-control-clean" 
                               value="<?php echo get_setting('smtp_password', $current_settings); ?>" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold mb-3">Sender Details</h6>

                <div class="mb-3">
                    <label class="form-label small text-muted fw-bold">From Email</label>
                    <input type="email" name="from_email" class="form-control form-control-clean" 
                           value="<?php echo get_setting('from_email', $current_settings); ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small text-muted fw-bold">From Name</label>
                    <input type="text" name="from_name" class="form-control form-control-clean" 
                           value="<?php echo get_setting('from_name', $current_settings); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary-soft w-100">
                    <i class="bi bi-save me-2"></i>Save Settings
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('smtpPassword');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
