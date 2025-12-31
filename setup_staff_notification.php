<?php
require_once 'includes/db.php';

echo "<h2>Setup Staff Notification Workflow</h2>";

// Create Workflow
$sql = "INSERT INTO workflows (workflow_name, event_type, status) VALUES ('Notify Staff on New Order', 'order_booked', 'active')";
if ($conn->query($sql) === TRUE) {
    $workflow_id = $conn->insert_id;
    echo "Workflow created with ID: $workflow_id<br>";
    
    // Create Action
    $sql2 = "INSERT INTO workflow_actions (workflow_id, action_type) VALUES ($workflow_id, 'send_email')";
    if ($conn->query($sql2) === TRUE) {
        $action_id = $conn->insert_id;
        echo "Action created with ID: $action_id<br>";
        
        // Create Email Template
        $to_email = '{{staff_email}}';
        $subject = 'New Order Assignment - {{order_code}}';
        $body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #007bff;">New Order Assignment</h2>
            <p>Hello <strong>{{staff_name}}</strong>,</p>
            <p>You have been assigned a new order:</p>
            
            <table style="border-collapse: collapse; width: 100%; margin: 20px 0;">
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">Order ID:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{order_code}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">Customer:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{customer_name}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">Phone:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{customer_phone}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">Date & Time:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{booking_date}} at {{booking_time}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">Pickup Address:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{pickup_address}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">Delivery Address:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{delivery_address}}</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd; background: #f8f9fa; font-weight: bold;">Total Amount:</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{total_price}}</td>
                </tr>
            </table>
            
            <p style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <strong>Important:</strong> Please ensure you arrive on time and provide excellent service to our customer.
            </p>
            
            <p>Best regards,<br><strong>DR.SPIN Team</strong></p>
        </div>';
        
        $stmt = $conn->prepare("INSERT INTO workflow_emails (action_id, to_email_field, email_subject, email_content) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $action_id, $to_email, $subject, $body);
        
        if ($stmt->execute()) {
            echo "Email template created successfully!<br>";
            echo "<br><strong>✓ Staff notification workflow is now active!</strong><br>";
            echo "<p>Staff members will receive an email notification when they are assigned to a new order.</p>";
        } else {
            echo "Error creating email template: " . $stmt->error;
        }
    } else {
        echo "Error creating action: " . $conn->error;
    }
} else {
    echo "Error creating workflow: " . $conn->error;
}

echo "<br><p><a href='admin/automation.php'>View Automation Dashboard</a></p>";
?>
