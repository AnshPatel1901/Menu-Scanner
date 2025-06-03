<?php
session_start();
include '../../includes/db.php';

// Redirect if not logged in as chef
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'chef') {
    header("Location: ../../auth/login.php");
    exit();
}

// Handle order status update (Pending, In Progress, Completed, or Cancelled)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Prepare the update query
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        // Redirect to refresh the page and reflect the changes
        header("Location: /digidine/pages/chef/chef_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error updating order status');</script>";
    }
    $stmt->close();
}

// Fetch orders by status
$pending_orders_query = "SELECT * FROM orders WHERE status='pending'";
$in_progress_orders_query = "SELECT * FROM orders WHERE status='in_progress'";
$completed_orders_query = "SELECT * FROM orders WHERE status='completed'";
$cancelled_orders_query = "SELECT * FROM orders WHERE status='cancelled'";

$pending_orders_result = $conn->query($pending_orders_query);
$in_progress_orders_result = $conn->query($in_progress_orders_query);
$completed_orders_result = $conn->query($completed_orders_query);
$cancelled_orders_result = $conn->query($cancelled_orders_query);

$pending_orders_count = $pending_orders_result->num_rows;
$in_progress_orders_count = $in_progress_orders_result->num_rows;
$completed_orders_count = $completed_orders_result->num_rows;
$cancelled_orders_count = $cancelled_orders_result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chef Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #1e1e1e; /* Dark background */
            font-family: 'Poppins', sans-serif;
            color: #fff;
        }
        .container {
            max-width: 1200px;
            margin-top: 60px;
        }
        h1 {
            text-align: center;
            color: #f39c12; /* Accent color */
            font-size: 3rem;
            margin-bottom: 40px;
        }
        .card {
            background-color: #2b2b2b; /* Dark card background */
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        .card-header {
            background-color: #333333; /* Muted dark header */
            color: #fff;
            font-weight: bold;
            font-size: 1.5rem;
            padding: 20px;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.1rem;
            border: 1px solid #444;
            border-radius: 10px;
            margin-bottom: 15px;
            background-color: #333333; /* Dark list items */
            color: #fff;
        }
        .list-group-item span {
            display: inline-block;
        }
        .badge-warning {
            background-color: #f39c12; /* Orange badge for pending */
            font-size: 1rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .badge-info {
            background-color: #3498db; /* Blue badge for in progress */
            font-size: 1rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .badge-success {
            background-color: #27ae60; /* Green badge for completed */
            font-size: 1rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .badge-danger {
            background-color: #e74c3c; /* Red badge for cancelled */
            font-size: 1rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .list-group-item:hover {
            background-color: #444;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .badge {
            font-size: 0.9rem;
        }
        .row {
            margin-bottom: 20px;
        }
        .order-action-btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Chef Dashboard</h1>

    <div class="row">
        <!-- Pending Orders Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Pending Orders
                </div>
                <div class="card-body">
                    <h5 class="card-title">Total Pending Orders: <?php echo $pending_orders_count; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $pending_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <span>Order #<?php echo $order['id']; ?> - Total: ₹<?php echo $order['total_price']; ?></span>
                                <span class="badge badge-warning">Pending</span>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="btn btn-sm btn-info order-action-btn">Mark as In Progress</button>
                                </form>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-sm btn-success order-action-btn">Mark as Completed</button>
                                </form>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-sm btn-danger order-action-btn">Cancel Order</button>
                                </form>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- In Progress Orders Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    In Progress Orders
                </div>
                <div class="card-body">
                    <h5 class="card-title">Total In Progress Orders: <?php echo $in_progress_orders_count; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $in_progress_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <span>Order #<?php echo $order['id']; ?> - Total: ₹<?php echo $order['total_price']; ?></span>
                                <span class="badge badge-info">In Progress</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Completed Orders Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Completed Orders
                </div>
                <div class="card-body">
                    <h5 class="card-title">Total Completed Orders: <?php echo $completed_orders_count; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $completed_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <span>Order #<?php echo $order['id']; ?> - Total: ₹<?php echo $order['total_price']; ?></span>
                                <span class="badge badge-success">Completed</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Cancelled Orders Card -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Cancelled Orders
                </div>
                <div class="card-body">
                    <h5 class="card-title">Total Cancelled Orders: <?php echo $cancelled_orders_count; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $cancelled_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <span>Order #<?php echo $order['id']; ?> - Total: ₹<?php echo $order['total_price']; ?></span>
                                <span class="badge badge-danger">Cancelled</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
