<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/functions.php';
requireLogin();

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dr. Spin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: var(--white);
            border-right: 1px solid #eee;
        }
        .nav-link {
            color: var(--text-dark);
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: #f8fbff;
            color: var(--primary-color);
            font-weight: 600;
        }
        .nav-link i {
            margin-right: 10px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row">
        <!-- Mobile Header -->
        <div class="d-md-none bg-white border-bottom sticky-top">
            <div class="d-flex justify-content-between align-items-center p-3">
                <a href="dashboard.php" class="text-decoration-none">
                    <img src="../assets/images/afe35db6-37e4-471e-92e8-25c35ce8a7bf.png" alt="Dr. Spin Logo" style="height: 40px;">
                </a>
                <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <i class="bi bi-list fs-4"></i>
                </button>
            </div>
        </div>

        <!-- Desktop Sidebar -->
        <div class="col-md-3 col-lg-2 px-0 admin-sidebar d-none d-md-block">
            <div class="p-4 border-bottom">
                <a href="dashboard.php" class="text-decoration-none">
                    <img src="../assets/images/afe35db6-37e4-471e-92e8-25c35ce8a7bf.png" alt="Dr. Spin Logo" class="img-fluid" style="max-height: 50px;">
                </a>
            </div>
            <div class="p-3">
                <nav class="nav flex-column">
                    <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                        <i class="bi bi-receipt"></i> Orders
                    </a>
                    <a class="nav-link <?php echo $current_page == 'staff.php' ? 'active' : ''; ?>" href="staff.php">
                        <i class="bi bi-people"></i> Staff
                    </a>
                    <a class="nav-link <?php echo $current_page == 'services.php' ? 'active' : ''; ?>" href="services.php">
                        <i class="bi bi-box-seam"></i> Services
                    </a>
                    <a class="nav-link <?php echo $current_page == 'cities.php' ? 'active' : ''; ?>" href="cities.php">
                        <i class="bi bi-geo-alt"></i> Cities
                    </a>
                    <a class="nav-link <?php echo $current_page == 'automation.php' ? 'active' : ''; ?>" href="automation.php">
                        <i class="bi bi-lightning"></i> Automation
                    </a>
                    <a class="nav-link <?php echo $current_page == 'payments.php' ? 'active' : ''; ?>" href="payments.php">
                        <i class="bi bi-credit-card"></i> Payments
                    </a>
                    <a class="nav-link <?php echo $current_page == 'analytics.php' ? 'active' : ''; ?>" href="analytics.php">
                        <i class="bi bi-graph-up"></i> Analytics
                    </a>
                    <a class="nav-link ps-5 <?php echo $current_page == 'users_list.php' ? 'active' : ''; ?>" href="users_list.php">
                        <i class="bi bi-people"></i> User List
                    </a>
                    <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="users.php">
                        <i class="bi bi-person-gear"></i> Admin List
                    </a>
                    <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                    <a class="nav-link text-danger mt-5" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>
        </div>

        <!-- Mobile Offcanvas Menu -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu">
            <div class="offcanvas-header border-bottom">
                <img src="../assets/images/afe35db6-37e4-471e-92e8-25c35ce8a7bf.png" alt="Dr. Spin Logo" style="height: 40px;">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-3">
                <nav class="nav flex-column">
                    <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                        <i class="bi bi-receipt"></i> Orders
                    </a>
                    <a class="nav-link <?php echo $current_page == 'staff.php' ? 'active' : ''; ?>" href="staff.php">
                        <i class="bi bi-people"></i> Staff
                    </a>
                    <a class="nav-link <?php echo $current_page == 'services.php' ? 'active' : ''; ?>" href="services.php">
                        <i class="bi bi-box-seam"></i> Services
                    </a>
                    <a class="nav-link <?php echo $current_page == 'cities.php' ? 'active' : ''; ?>" href="cities.php">
                        <i class="bi bi-geo-alt"></i> Cities
                    </a>
                    <a class="nav-link <?php echo $current_page == 'automation.php' ? 'active' : ''; ?>" href="automation.php">
                        <i class="bi bi-lightning"></i> Automation
                    </a>
                    <a class="nav-link <?php echo $current_page == 'payments.php' ? 'active' : ''; ?>" href="payments.php">
                        <i class="bi bi-credit-card"></i> Payments
                    </a>
                    <a class="nav-link <?php echo $current_page == 'analytics.php' ? 'active' : ''; ?>" href="analytics.php">
                        <i class="bi bi-graph-up"></i> Analytics
                    </a>
                    <a class="nav-link ps-5 <?php echo $current_page == 'users_list.php' ? 'active' : ''; ?>" href="users_list.php">
                        <i class="bi bi-people"></i> User List
                    </a>
                    <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="users.php">
                        <i class="bi bi-person-gear"></i> Users
                    </a>
                    <a class="nav-link text-danger mt-5" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
