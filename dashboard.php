<?php
ini_set('session.cookie_lifetime', 0); // Ensure session cookies expire when the browser is closed
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: auth/login.php");
    exit();
}

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include 'includes/db.php';

$userRole = $_SESSION['role'];
$username = $_SESSION['username'];

switch ($userRole) {
    case 'admin':
        $dashboardContent = "Welcome Admin! You can manage the menu, users, and orders.";
        break;
    case 'chef':
        $dashboardContent = "Welcome Chef! You can view and update orders.";
        break;
    case 'manager':
        $dashboardContent = "Welcome Manager! You can view all orders and manage order status.";
        break;
    default:
        $dashboardContent = "Welcome! You have access to basic features.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #f0f0f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        h2 {
            text-align: center;
            margin: 40px 0;
            font-size: 2.5rem;
            color: #ff6600;
            text-shadow: 0 0 10px rgba(255, 102, 0, 0.7);
        }

        .section-card {
            background: #1e1e1e;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            transition: transform 0.4s ease, background 0.4s ease;
        }

        .section-card:hover {
            transform: scale(1.02);
            background: #292929;
        }

        .section-title {
            color: #ffcc70;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .btn-custom {
        text-decoration: none !important;
        color: #fff !important;
        background: #ff6600;
        padding: 10px 20px;
        border-radius: 30px;
        margin: 5px;
        display: inline-block;
        transition: background 0.3s;
        font-weight: bold;
        }

        .btn-custom:hover,
        .btn-custom:focus,
        .btn-custom:active,
        .btn-custom:visited {
            text-decoration: none !important;
            color: #fff !important;
            background: #e65500;
        }

        .logout-btn {
            display: block;
            margin-top: 20px;
            text-align: center;
        }

        .logout-btn a {
            background-color: #ff6600;
        }

        .logout-btn a:hover {
            background-color: #e65500;
        }
    </style>
</head>

<body>
<div class="container mt-5">
    <h2>🏠 Welcome to the Dashboard</h2>

    <div class="section-card">
        <h4 class="section-title">Hello, <?php echo $username; ?>!</h4>
        <p><?php echo $dashboardContent; ?></p>
        <div class="logout-btn">
            <a href="auth/logout.php" class="btn-custom">🔓 Logout</a>
        </div>
    </div>

    <?php if ($userRole == 'admin'): ?>
        <div class="section-card">
            <h4 class="section-title">🛠️ Admin Actions</h4>
            <p>You can manage the menu, users, and view all orders.</p>
            <a href="functions/manage_menu.php" class="btn-custom">Manage Menu</a>
            <a href="functions/manage_users.php" class="btn-custom">Manage Users</a>
            <a href="pages/admin/admin_dashboard.php" class="btn-custom">View Orders</a>
        </div>
    <?php endif; ?>

    <?php if ($userRole == 'chef'): ?>
        <div class="section-card">
            <h4 class="section-title">👨‍🍳 Chef Actions</h4>
            <p>You can view and update orders.</p>
            <a href="pages/chef/chef_dashboard.php" class="btn-custom">View Orders</a>
        </div>
    <?php endif; ?>

    <?php if ($userRole == 'manager'): ?>
        <div class="section-card">
            <h4 class="section-title">📋 Manager Actions</h4>
            <p>You can view and manage all orders.</p>
            <a href="pages/manager/manager_dashboard.php" class="btn-custom">View Orders</a>
        </div>
    <?php endif; ?>

    <?php if ($userRole == 'customer'): ?>
        <div class="section-card">
            <h4 class="section-title">🛒 Your Orders</h4>
            <p>Check your current and past orders here.</p>
            <a href="pages/customer/customer_orders.php" class="btn-custom">View My Orders</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
