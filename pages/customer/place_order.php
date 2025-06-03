<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get all cart items for this user
$cart_query = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();

if ($cart_result->num_rows == 0) {
    echo "Your cart is empty!";
    exit();
}

// Calculate total price
$total_price = 0;
while ($row = $cart_result->fetch_assoc()) {
    $total_price += $row['item_price'] * $row['item_qty'];
}

// Insert into orders table
$insert_order = $conn->prepare("INSERT INTO orders (customer_name, contact_number, total_price, status) VALUES (?, ?, ?, 'pending')");
$contact = "N/A"; // You can collect contact in future if needed
$insert_order->bind_param("ssd", $username, $contact, $total_price);
$insert_order->execute();

// Clear cart after placing order
$delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$delete_cart->bind_param("i", $user_id);
$delete_cart->execute();

// Redirect to orders page or dashboard
header("Location: /digidine/pages/customer/customer_orders.php?success=1");
exit();
