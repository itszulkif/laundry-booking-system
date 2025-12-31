<?php
require_once '../includes/db.php';

echo "Creating Workflows Database Schema...\n\n";

// Create workflows table
$workflows_sql = "CREATE TABLE IF NOT EXISTS workflows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_name VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    event_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($workflows_sql) === TRUE) {
    echo "✓ Table 'workflows' created successfully.\n";
} else {
    echo "✗ Error creating 'workflows' table: " . $conn->error . "\n";
}

// Create workflow_actions table
$actions_sql = "CREATE TABLE IF NOT EXISTS workflow_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_id INT NOT NULL,
    action_type VARCHAR(100) NOT NULL,
    action_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workflow_id) REFERENCES workflows(id) ON DELETE CASCADE,
    INDEX idx_workflow_id (workflow_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($actions_sql) === TRUE) {
    echo "✓ Table 'workflow_actions' created successfully.\n";
} else {
    echo "✗ Error creating 'workflow_actions' table: " . $conn->error . "\n";
}

// Create workflow_emails table
$emails_sql = "CREATE TABLE IF NOT EXISTS workflow_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action_id INT NOT NULL,
    to_email_field VARCHAR(255) NOT NULL,
    email_subject VARCHAR(500) NOT NULL,
    email_content LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (action_id) REFERENCES workflow_actions(id) ON DELETE CASCADE,
    INDEX idx_action_id (action_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($emails_sql) === TRUE) {
    echo "✓ Table 'workflow_emails' created successfully.\n";
} else {
    echo "✗ Error creating 'workflow_emails' table: " . $conn->error . "\n";
}

// Create activity_log table for tracking workflow executions
$log_sql = "CREATE TABLE IF NOT EXISTS workflow_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    workflow_id INT NOT NULL,
    order_id INT,
    action_type VARCHAR(100),
    status ENUM('success', 'failed') NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workflow_id) REFERENCES workflows(id) ON DELETE CASCADE,
    INDEX idx_workflow_id (workflow_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($log_sql) === TRUE) {
    echo "✓ Table 'workflow_activity_log' created successfully.\n";
} else {
    echo "✗ Error creating 'workflow_activity_log' table: " . $conn->error . "\n";
}

echo "\nDatabase schema creation completed!\n";
?>
