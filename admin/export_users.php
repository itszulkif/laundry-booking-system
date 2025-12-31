<?php
require_once '../includes/db.php';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="booking_users_' . date('Y-m-d_His') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

// Fetch all unique customers from orders
$query = "SELECT DISTINCT 
            customer_name as Name,
            customer_email as Email,
            customer_phone as Phone,
            MIN(created_at) as 'First Booking',
            MAX(created_at) as 'Last Booking',
            COUNT(*) as 'Total Bookings'
          FROM orders 
          GROUP BY customer_email, customer_name, customer_phone
          ORDER BY customer_name ASC";

$result = $conn->query($query);

// Start Excel output
echo '<?xml version="1.0"?>';
echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:html="http://www.w3.org/TR/REC-html40">';

echo '<Worksheet ss:Name="Booking Users">';
echo '<Table>';

// Header Row
echo '<Row>';
echo '<Cell><Data ss:Type="String">Name</Data></Cell>';
echo '<Cell><Data ss:Type="String">Email</Data></Cell>';
echo '<Cell><Data ss:Type="String">Phone</Data></Cell>';
echo '<Cell><Data ss:Type="String">First Booking</Data></Cell>';
echo '<Cell><Data ss:Type="String">Last Booking</Data></Cell>';
echo '<Cell><Data ss:Type="String">Total Bookings</Data></Cell>';
echo '</Row>';

// Data Rows
while ($row = $result->fetch_assoc()) {
    echo '<Row>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['Name']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['Email']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['Phone']) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . date('Y-m-d H:i', strtotime($row['First Booking'])) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="String">' . date('Y-m-d H:i', strtotime($row['Last Booking'])) . '</Data></Cell>';
    echo '<Cell><Data ss:Type="Number">' . $row['Total Bookings'] . '</Data></Cell>';
    echo '</Row>';
}

echo '</Table>';
echo '</Worksheet>';
echo '</Workbook>';

$conn->close();
?>
