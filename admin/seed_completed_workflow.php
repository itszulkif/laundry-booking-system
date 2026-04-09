<?php
require_once __DIR__ . '/../includes/db.php';

echo "Seeding Order Completed Workflow...\n\n";

// 1. Create Workflow
$workflow_name = "Order Completed Notification";
$event_type = "order_completed";
$status = "active";

$stmt = $conn->prepare("INSERT INTO workflows (workflow_name, event_type, status) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $workflow_name, $event_type, $status);

if ($stmt->execute()) {
    $workflow_id = $conn->insert_id;
    echo "✓ Created Workflow ID: $workflow_id\n";
    
    // 2. Create Action (Send Email)
    $action_type = "send_email";
    $stmt_action = $conn->prepare("INSERT INTO workflow_actions (workflow_id, action_type) VALUES (?, ?)");
    $stmt_action->bind_param("is", $workflow_id, $action_type);
    
    if ($stmt_action->execute()) {
        $action_id = $conn->insert_id;
        echo "✓ Created Action ID: $action_id\n";
        
        // 3. Configure Email
        $to_email = "{{customer_email}}";
        $subject = "Order Completed";
        $content = "Your order has been completed. Please make the payment using PayID: 0431500738.";
        
        $stmt_email = $conn->prepare("INSERT INTO workflow_emails (action_id, to_email_field, email_subject, email_content) VALUES (?, ?, ?, ?)");
        $stmt_email->bind_param("isss", $action_id, $to_email, $subject, $content);
        
        if ($stmt_email->execute()) {
            echo "✓ Configured Email Template\n";
        } else {
            echo "✗ Error configuring email: " . $conn->error . "\n";
        }
    } else {
        echo "✗ Error creating action: " . $conn->error . "\n";
    }
} else {
    echo "✗ Error creating workflow: " . $conn->error . "\n";
}

echo "\nSeeding completed!\n";
?>
