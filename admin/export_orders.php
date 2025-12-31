<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d_His') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Fetch all orders
$query = "SELECT o.*, s.name as staff_name, c.name as city_name 
          FROM orders o 
          LEFT JOIN staff s ON o.staff_id = s.id 
          LEFT JOIN cities c ON o.city_id = c.id 
          ORDER BY o.created_at DESC";

$result = $conn->query($query);

// Start Excel output
echo '<?xml version="1.0"?>';
echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:html="http://www.w3.org/TR/REC-html40">';

echo '<Worksheet ss:Name="Orders">';
echo '<Table>';

// Header Row
echo '<Row>';
echo '<Cell><Data ss:Type="String">Order ID</Data></Cell>';
echo '<Cell><Data ss:Type="String">Customer Name</Data></Cell>';
echo '<Cell><Data ss:Type="String">Email</Data></Cell>';
echo '<Cell><Data ss:Type="String">Phone</Data></Cell>';
echo '<Cell><Data ss:Type="String">City</Data></Cell>';
echo '<Cell><Data ss:Type="String">Address</Data></Cell>';
echo '<Cell><Data ss:Type="String">Service Date</Data></Cell>';
echo '<Cell><Data ss:Type="String">Time</Data></Cell>';
echo '<Cell><Data ss:Type="String">Staff</Data></Cell>';
echo '<Cell><Data ss:Type="String">Total Price</Data></Cell>';
echo '<Cell><Data ss:Type="String">Status</Data></Cell>';
echo '<Cell><Data ss:Type="String">Created At</Data></Cell>';
echo '</Row>';

// Data Rows
while ($row = $result->fetch_assoc()) {
    echo '<Row>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['order_code']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['customer_name']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['customer_email']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['customer_phone']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['city_name']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['pickup_address']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . date('Y-m-d', strtotime($row['booking_date'])) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . date('H:i', strtotime($row['booking_time'])) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['staff_name']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="Number">' . $row['total_price'] . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['status']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . date('Y-m-d H:i', strtotime($row['created_at'])) . '</Data></Cell>';
    echo '</Row>';
}

echo '</Table>';
echo '</Worksheet>';
echo '</Workbook>';

$conn->close();
?>
