<?php
session_start();
include '../../includes/db.php';

// Redirect if not logged in as manager
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    header("Location: ../../auth/login.php");
    exit();
}

// Fetch orders based on status
$pending_orders_query = "SELECT * FROM orders WHERE status='pending'";
$completed_orders_query = "SELECT * FROM orders WHERE status='completed'";
$in_progress_orders_query = "SELECT * FROM orders WHERE status='in_progress'";

$pending_orders_result = $conn->query($pending_orders_query);
$completed_orders_result = $conn->query($completed_orders_query);
$in_progress_orders_result = $conn->query($in_progress_orders_query);

// Handle adding a chef
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_chef'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'chef')");
    $stmt->bind_param("ss", $username, $password);
    if ($stmt->execute()) {
        $message = "Chef added successfully!";
    } else {
        $message = "Error adding chef!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            margin-bottom: 50px;
            font-size: 3rem;
        }

        h2 {
            color: #ff6600;
            margin-top: 40px;
            font-size: 1.8rem;
        }

        /* Card Styles */
        .card {
            background-color: #282828;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            overflow: hidden;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-body ul::-webkit-scrollbar {
            width: 8px;
        }

        .card-body ul::-webkit-scrollbar-track {
            background: transparent;
        }

        .card-body ul::-webkit-scrollbar-thumb {
            background-color: #ff6600;
            border-radius: 10px;
        }

        .card:active {
            transform: scale(0.98);
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(255, 102, 0, 0.3);
        }

        .card-header {
            background-color: #333;
            color: #fff;
            font-size: 1.3rem;
            font-weight: bold;
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
            font-weight: bold;
        }

        .badge-warning {
            background-color: #ff6600;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-info {
            background-color: #17a2b8;
        }

        .list-group-item {
            background-color: #444;
            color: #f0f0f0;
            border: none;
            font-size: 1rem;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #ff6600;
            color: #fff;
        }

        /* Button Styles */
        .btn-primary {
            background-color: #ff6600;
            border: none;
            border-radius: 30px;
            padding: 12px 20px;
            font-weight: bold;
            color: #fff;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e65500;
            transform: translateY(-3px);
        }

        /* Alert Styles */
        .alert-info {
            background-color: #17a2b8;
            color: #fff;
            font-weight: bold;
        }

        .form-group label {
            color: #f0f0f0;
            font-weight: bold;
        }

        .form-control {
            background-color: #555;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 1.1rem;
        }

        .form-control:focus {
            background-color: #666;
            border-color: #ff6600;
        }

        .container {
            max-width: 1200px;
            margin-top: 50px;
        }

        .row {
            margin-bottom: 50px;
        }

        /* Animation for loading elements */
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
    <h1>Manager Dashboard</h1>
    <hr style="border-top: 1px solid #555;">
    <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>

    <div class="row">
        <!-- Pending Orders -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Pending Orders
                </div>
                <div class="card-body">
                    <h5 class="card-title">Total: <?php echo $pending_orders_result->num_rows; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $pending_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                Order #<?php echo $order['id']; ?> - Total: ₹<?php echo $order['total_price']; ?>
                                <br><small class="text-muted">Placed on <?php echo date("M d, Y", strtotime($order['created_at'])); ?></small>
                               <span class="badge badge-warning float-right"><i class="fas fa-clock"></i> Pending</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    Completed Orders
                </div>
                <div class="card-body">
                    <h5 class="card-title">Total: <?php echo $completed_orders_result->num_rows; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $completed_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                Order #<?php echo $order['id']; ?> - Total: ₹<?php echo $order['total_price']; ?>
                                <br><small class="text-muted">Placed on <?php echo date("M d, Y", strtotime($order['created_at'])); ?></small>
                                <span class="badge badge-success float-right"><i class="fas fa-check-circle"></i> Completed</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- In Progress Orders -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    In Progress Orders
                </div>
                <div class="card-body">
                    <h5 class="card-title">Total: <?php echo $in_progress_orders_result->num_rows; ?></h5>
                    <ul class="list-group">
                        <?php while ($order = $in_progress_orders_result->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                Order #<?php echo $order['id']; ?> - Total: ₹<?php echo $order['total_price']; ?>
                                <br><small class="text-muted">Placed on <?php echo date("M d, Y", strtotime($order['created_at'])); ?></small>
                                <span class="badge badge-info float-right"><i class="fas fa-spinner"></i> In Progress</span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <h2>Add a Chef</h2>
    <hr style="border-top: 1px solid #555;">
    <form method="POST" action="manager_dashboard.php">
        <div class="form-group">
            <label for="username">Chef Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Chef Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" name="add_chef" class="btn btn-primary">Add Chef</button>
    </form>
    <br>
    <br>
    <!-- Back Button -->
    <a href="../../dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
