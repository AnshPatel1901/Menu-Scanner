<?php
session_start();
include '../../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

// Fetch Orders & Analysis
$pending_orders_result = $conn->query("SELECT * FROM orders WHERE status='pending'");
$completed_orders_result = $conn->query("SELECT * FROM orders WHERE status='completed'");
$in_progress_orders_result = $conn->query("SELECT * FROM orders WHERE status='in_progress'");
$daily_analysis_result = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as total_orders, SUM(total_price) as total_revenue FROM orders GROUP BY DATE(created_at)");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #1f1f1f;
            color: #f0f0f0;
            font-family: 'Poppins', sans-serif;
            margin-top: 50px;
        }
        h1 {
            text-align: center;
            color: #ff6600;
            text-shadow: 0 0 10px rgba(255, 102, 0, 0.7);
            margin-bottom: 40px;
            font-size: 3rem;
        }
        h2 {
            color: #ff6600;
            margin-top: 30px;
        }
        .container {
            max-width: 1200px;
            margin-top: 30px;
        }
        .card {
            background-color: #282828;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(255, 102, 0, 0.3);
        }
        .card-header {
            background-color: #333;
            color: #fff;
            font-size: 1.5rem;
            padding: 20px;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .list-group-item {
            background-color: #444;
            color: #fff;
            border: none;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }
        .list-group-item:hover {
            background-color: #ff6600;
            color: #fff;
        }
        .badge-warning { background: #ff6600; }
        .badge-success { background: #28a745; }
        .badge-info { background: #17a2b8; }
        .table {
            background-color: #333;
            color: #f0f0f0;
            border-radius: 15px;
            overflow: hidden;
        }
        .table thead {
            background-color: #444;
            color: #fff;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .table tbody tr:hover {
            background-color: #ff6600; /* Lighter background color */
            color: #fff; /* Ensuring text is visible */
        }

        .btn-primary, .btn-success, .btn-info, .btn-danger {
            border-radius: 30px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        .btn-primary:hover, .btn-success:hover, .btn-info:hover, .btn-danger:hover {
            transform: translateY(-3px);
        }
        a.btn {
            margin-top: 20px;
        }
        .fade-in {
            animation: fadeIn 1s ease-out;
        }
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
<div class="container fade-in">
    <h1>Admin Dashboard</h1>

    <!-- Orders Section -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">Pending Orders</div>
                <div class="card-body">
                    <h5>Total: <?php echo $pending_orders_result->num_rows; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $pending_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                Order #<?php echo $order['id']; ?> - ₹<?php echo $order['total_price']; ?>
                                <span class="badge badge-warning float-right">Pending</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">Completed Orders</div>
                <div class="card-body">
                    <h5>Total: <?php echo $completed_orders_result->num_rows; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $completed_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                Order #<?php echo $order['id']; ?> - ₹<?php echo $order['total_price']; ?>
                                <span class="badge badge-success float-right">Completed</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- In Progress Orders -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">In Progress Orders</div>
                <div class="card-body">
                    <h5>Total: <?php echo $in_progress_orders_result->num_rows; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $in_progress_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                Order #<?php echo $order['id']; ?> - ₹<?php echo $order['total_price']; ?>
                                <span class="badge badge-info float-right">In Progress</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Sales Analysis -->
    <div class="card mt-5">
        <div class="card-header bg-secondary text-white">Daily Sales Analysis</div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Orders</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($analysis = $daily_analysis_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $analysis['date']; ?></td>
                            <td><?php echo $analysis['total_orders']; ?></td>
                            <td>₹<?php echo $analysis['total_revenue']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Back Button -->
    <a href="../../dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
</div>
</body>
</html>
