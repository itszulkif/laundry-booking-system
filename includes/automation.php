<?php
require_once __DIR__ . '/../includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

/**
 * Execute workflows for a specific event
 * @param string $event_type - The event that triggered the workflow (e.g., 'order_booked')
 * @param int $order_id - The order ID that triggered the event
 */
function run_automation($event_type, $order_id) {
    global $conn;
    
    // Fetch the order details including staff information
    $order_query = "SELECT o.*, c.name as city_name, s.name as staff_name, s.email as staff_email 
                    FROM orders o 
                    LEFT JOIN cities c ON o.city_id = c.id 
                    LEFT JOIN staff s ON o.staff_id = s.id
                    WHERE o.id = $order_id";
    $order_result = $conn->query($order_query);
    
    if ($order_result->num_rows === 0) {
        return;
    }
    
    $order = $order_result->fetch_assoc();
    
    // Find all active workflows for this event type
    $workflows_query = "SELECT * FROM workflows 
                        WHERE event_type = '$event_type' 
                        AND status = 'active'";
    $workflows_result = $conn->query($workflows_query);
    
    while ($workflow = $workflows_result->fetch_assoc()) {
        $workflow_id = $workflow['id'];
        
        // Get all actions for this workflow
        $actions_query = "SELECT wa.*, we.to_email_field, we.email_subject, we.email_content
                          FROM workflow_actions wa
                          LEFT JOIN workflow_emails we ON wa.id = we.action_id
                          WHERE wa.workflow_id = $workflow_id";
        $actions_result = $conn->query($actions_query);
        
        while ($action = $actions_result->fetch_assoc()) {
            if ($action['action_type'] === 'send_email') {
                execute_send_email($action, $order, $workflow_id, $order_id);
            }
        }
    }
}

/**
 * Execute send email action
 */
function execute_send_email($action, $order, $workflow_id, $order_id) {
    global $conn;
    
    // Fetch Settings
    $settings = [];
    $settings_result = $conn->query("SELECT * FROM settings");
    while ($row = $settings_result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    // Replace dynamic variables
    $variables = [
        '{{customer_name}}' => $order['customer_name'],
        '{{customer_email}}' => $order['customer_email'],
        '{{customer_phone}}' => $order['customer_phone'],
        '{{order_code}}' => $order['order_code'],
        '{{booking_date}}' => date('F d, Y', strtotime($order['booking_date'])),
        '{{booking_time}}' => date('h:i A', strtotime($order['booking_time'])),
        '{{total_price}}' => '$' . number_format($order['total_price'], 2),
        '{{city_name}}' => $order['city_name'],
        '{{pickup_address}}' => $order['pickup_address'],
        '{{delivery_address}}' => $order['delivery_address'],
        '{{staff_name}}' => $order['staff_name'] ?? 'Not Assigned',
        '{{staff_email}}' => $order['staff_email'] ?? '',
        '{{admin_email}}' => $settings['from_email'] ?? 'admin@example.com'
    ];
    
    $to_email = str_replace(array_keys($variables), array_values($variables), $action['to_email_field']);
    $subject = str_replace(array_keys($variables), array_values($variables), $action['email_subject']);
    $body = str_replace(array_keys($variables), array_values($variables), $action['email_content']);
    
    // Send email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            
        $mail->Host       = $settings['smtp_host'];                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = $settings['smtp_username'];                     
        $mail->Password   = $settings['smtp_password'];
        
        if ($settings['smtp_encryption'] == 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($settings['smtp_encryption'] == 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPAutoTLS = false;
            $mail->SMTPSecure = false;
        }
        
        $mail->Port       = $settings['smtp_port'];                                    

        //Recipients
        $mail->setFrom($settings['from_email'], $settings['from_name']);
        $mail->addAddress($to_email);     

        //Content
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = $body;
        
        $mail->send();
        $success = true;
        $details = "Email sent to $to_email";
    } catch (Exception $e) {
        $success = false;
        $details = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
    // Log the activity
    $status = $success ? 'success' : 'failed';
    
    $stmt = $conn->prepare("INSERT INTO workflow_activity_log (workflow_id, order_id, action_type, status, details) VALUES (?, ?, 'send_email', ?, ?)");
    $stmt->bind_param("iiss", $workflow_id, $order_id, $status, $details);
    $stmt->execute();
}
?>
