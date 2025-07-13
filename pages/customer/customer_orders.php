<?php
ini_set('session.cookie_lifetime', 0); // Ensure session cookies expire when the browser is closed
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../../auth/login.php");
    exit();
}

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        o.id AS order_id,
        o.created_at,
        o.total_price,
        o.status,
        m.name AS item_name,
        m.price AS item_price,
        oi.quantity AS item_qty
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN menu m ON oi.menu_id = m.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
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

        .order-item {
            background: #1e1e1e;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
        }

        .order-item:hover {
            transform: scale(1.02);
            background: #292929;
            box-shadow: 0 0 20px 5px rgba(255, 102, 0, 0.5);
        }

        .order-header {
            font-weight: bold;
            color: #ffcc70;
        }

        ul {
            padding-left: 20px;
        }

        ul li {
            margin-bottom: 6px;
        }

        .btn-orange {
            background-color: #ff6600;
            color: #fff !important;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: bold;
            transition: background 0.3s ease;
            display: inline-block;
            text-decoration: none;
            margin: 10px 5px;
        }

        .btn-orange:hover {
            background-color: #e65500;
            color: #fff !important;
            text-decoration: none;
        }

        .back-btn {
            text-align: center;
            margin-top: 30px;
        }

        .back-btn a {
            text-decoration: none;
            color: #fff;
            background: #ff6600;
            padding: 10px 20px;
            border-radius: 30px;
            transition: background 0.3s;
            font-weight: bold;
        }

        .back-btn a:hover {
            background: #e65500;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>📦 Your Orders</h2>

    <?php if ($orders->num_rows > 0): ?>
        <?php
        $current_order_id = null;
        $order_items = [];
        while ($order = $orders->fetch_assoc()):
            if ($current_order_id != $order['order_id']):
                if ($current_order_id != null): ?>
                    <div class="order-item">
                        <h5 class="order-header">Order ID: <?php echo $current_order_id; ?></h5>
                        <p>Status: <?php echo ucfirst($order_items[0]['status']); ?></p>
                        <p><strong>Total Price:</strong> ₹<?php echo number_format($order_items[0]['total_price'], 2); ?></p>
                        <p><small>Ordered on: <?php echo date('d M Y, h:i A', strtotime($order_items[0]['created_at'])); ?></small></p>
                        <h6>Order Items:</h6>
                        <ul>
                            <?php foreach ($order_items as $item): ?>
                                <li><?php echo htmlspecialchars($item['item_name']); ?> - ₹<?php echo $item['item_price']; ?> x <?php echo $item['item_qty']; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif;
                $current_order_id = $order['order_id'];
                $order_items = [];
            endif;

            $order_items[] = [
                'order_id' => $order['order_id'],
                'item_name' => $order['item_name'],
                'item_price' => $order['item_price'],
                'item_qty' => $order['item_qty'],
                'status' => $order['status'],
                'total_price' => $order['total_price'],
                'created_at' => $order['created_at']
            ];
        endwhile;

        if ($current_order_id != null): ?>
            <div class="order-item">
                <h5 class="order-header">Order ID: <?php echo $current_order_id; ?></h5>
                <p>Status: <?php echo ucfirst($order_items[0]['status']); ?></p>
                <p><strong>Total Price:</strong> ₹<?php echo number_format($order_items[0]['total_price'], 2); ?></p>
                <p><small>Ordered on: <?php echo date('d M Y, h:i A', strtotime($order_items[0]['created_at'])); ?></small></p>
                <h6>Order Items:</h6>
                <ul>
                    <?php foreach ($order_items as $item): ?>
                        <li><?php echo htmlspecialchars($item['item_name']); ?> - ₹<?php echo $item['item_price']; ?> x <?php echo $item['item_qty']; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="text-center mt-5">
            <p>You haven't placed any orders yet.</p>
            <a href="menu.php" class="btn-orange">Order Now</a>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
    <a href="menu.php" class="btn-orange">➕ Continue Ordering</a>
    </div>

    <div class="back-btn">
        <a href="/digidine/dashboard.php">🔙 Back to Dashboard</a>
    </div>
</div>
</body>
</html>
