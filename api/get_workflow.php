<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

// Fetch workflow with action and email details
$query = "SELECT w.*, wa.action_type, we.to_email_field as to_email, we.email_subject, we.email_content
          FROM workflows w
          LEFT JOIN workflow_actions wa ON w.id = wa.workflow_id
          LEFT JOIN workflow_emails we ON wa.id = we.action_id
          WHERE w.id = $id
          LIMIT 1";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $workflow = $result->fetch_assoc();
    echo json_encode($workflow);
} else {
    echo json_encode(['error' => 'Workflow not found']);
}
?>
