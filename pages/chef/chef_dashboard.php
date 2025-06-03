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

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        header("Location: /digidine/pages/chef/chef_dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error updating order status');</script>";
    }
    $stmt->close();
}

// Fetch orders by status
$pending_orders_result = $conn->query("SELECT * FROM orders WHERE status='pending'");
$in_progress_orders_result = $conn->query("SELECT * FROM orders WHERE status='in_progress'");
$completed_orders_result = $conn->query("SELECT * FROM orders WHERE status='completed'");
$cancelled_orders_result = $conn->query("SELECT * FROM orders WHERE status='cancelled'");

$pending_orders_count = $pending_orders_result->num_rows;
$in_progress_orders_count = $in_progress_orders_result->num_rows;
$completed_orders_count = $completed_orders_result->num_rows;
$cancelled_orders_count = $cancelled_orders_result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Chef Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
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
        margin-bottom: 50px;
        font-size: 3rem;
    }
    .card {
        background-color: #282828;
        border-radius: 15px;
        box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        margin-bottom: 20px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(255,102,0,0.3);
    }
    .card-header {
        background-color: #333;
        color: #fff;
        font-size: 1.3rem;
        font-weight: 600;
        padding: 20px;
    }
    .card-body {
        padding: 20px;
        font-size: 1.1rem;
    }
    .badge {
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    .badge-warning { background-color: #ff6600; }
    .badge-info { background-color: #17a2b8; }
    .badge-success { background-color: #28a745; }
    .badge-danger { background-color: #dc3545; }
    .list-group-item {
        background-color: #444;
        color: #f0f0f0;
        border: none;
        font-size: 1rem;
        border-radius: 10px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .list-group-item:hover {
        background-color: #ff6600;
        color: #fff;
    }
    .list-group-item form {
  display: inline-block;
  margin-right: 10px; /* space between buttons */
}

.list-group-item form:last-child {
  margin-right: 0; /* no margin after last button */
}

.list-group-item form button.order-action-btn {
  min-width: 100px;    /* consistent button width */
  padding: 6px 12px;
  font-size: 0.9rem;
  cursor: pointer;
}

    form {
        display: inline-block;
        margin-left: 8px;
    }
    .order-action-btn {
        border-radius: 30px;
        padding: 5px 12px;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .order-action-btn:hover {
        transform: translateY(-3px);
    }
    .btn-info {
        background-color: #17a2b8;
        color: white;
    }
    .btn-info:hover {
        background-color: #138496;
    }
    .btn-success {
        background-color: #28a745;
        color: white;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
    .btn-danger:hover {
        background-color: #c82333;
    }
    .container {
        max-width: 1200px;
        margin-top: 20px;
    }
    .row {
        margin-bottom: 50px;
    }
    a.btn-back {
        display: inline-block;
        background-color: #ff6600;
        color: #fff;
        padding: 12px 24px;
        font-weight: 600;
        border-radius: 30px;
        text-decoration: none;
        box-shadow: 0 6px 15px rgba(255,102,0,0.5);
        transition: background-color 0.3s ease, transform 0.3s ease;
        margin-top: 30px;
    }
    a.btn-back:hover {
        background-color: #e65500;
        transform: translateY(-3px);
        color: #fff;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Chef Dashboard</h1>

    <div class="row">
        <!-- Pending Orders -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Pending Orders</div>
                <div class="card-body">
                    <h5>Total Pending Orders: <?php echo $pending_orders_count; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $pending_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <span>Order #<?php echo $order['id']; ?> - Total: ₹<?php echo $order['total_price']; ?></span>
                                <span class="badge badge-warning">Pending</span>
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="order-action-btn btn-info">Mark as In Progress</button>
                                </form>
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="order-action-btn btn-success">Mark as Completed</button>
                                </form>
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="order-action-btn btn-danger">Cancel Order</button>
                                </form>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- In Progress Orders -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">In Progress Orders</div>
                <div class="card-body">
                    <h5>Total In Progress Orders: <?php echo $in_progress_orders_count; ?></h5>
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

        <!-- Completed Orders -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Completed Orders</div>
                <div class="card-body">
                    <h5>Total Completed Orders: <?php echo $completed_orders_count; ?></h5>
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

        <!-- Cancelled Orders -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Cancelled Orders</div>
                <div class="card-body">
                    <h5>Total Cancelled Orders: <?php echo $cancelled_orders_count; ?></h5>
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

    <div class="text-center">
        <a href="../../dashboard.php" class="btn-back"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
