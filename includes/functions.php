<?php
function sanitize($conn, $input) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($input))));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/bookingsytem/admin/login.php');
    }
}

// Format price helper
function formatPrice($price) {
    return '$' . number_format($price, 2);
}
?>
