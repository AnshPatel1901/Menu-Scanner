<?php
ini_set('session.cookie_lifetime', 0); // Ensure session cookies expire when the browser is closed
session_start();
require_once '../../includes/db.php';

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["user_id"])) { // Assuming 'user_id' is set upon login
    header("location: /digidine/auth/login.php");
    exit;
}

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Add to Cart Logic
if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $item_qty = $_POST['item_qty'];

    $cart_item = [
        'id' => $item_id,
        'name' => $item_name,
        'price' => $item_price,
        'qty' => $item_qty
    ];

    if (isset($_SESSION['cart'])) {
        $ids = array_column($_SESSION['cart'], 'id');
        if (in_array($item_id, $ids)) {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $item_id) {
                    $item['qty'] += $item_qty;
                }
            }
        } else {
            $_SESSION['cart'][] = $cart_item;
        }
    } else {
        $_SESSION['cart'][] = $cart_item;
    }
    header("Location: /digidine/pages/customer/cart.php");
    exit();
}

// Remove item from cart
if (isset($_POST['remove_item'])) {
    $item_id = $_POST['item_id'];
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($item) use ($item_id) {
        return $item['id'] != $item_id;
    });
    header("Location: /digidine/pages/customer/cart.php");
    exit();
}

// Update item quantity in cart
if (isset($_POST['update_qty'])) {
    $item_id = $_POST['item_id'];
    $new_qty = intval($_POST['item_qty']); // Ensure it's an integer

    if ($new_qty <= 0) { // If quantity is 0 or less, remove the item
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($cart_item_filter) use ($item_id) {
            return $cart_item_filter['id'] != $item_id;
        });
    } else { // Otherwise, update the quantity
        foreach ($_SESSION['cart'] as &$item_in_cart) {
            if ($item_in_cart['id'] == $item_id) {
                $item_in_cart['qty'] = $new_qty;
                break;
            }
        }
        unset($item_in_cart);
    }
    header("Location: /digidine/pages/customer/cart.php");
    exit();
}

// Place Order Logic
if (isset($_POST['place_order']) && !empty($_SESSION['cart'])) {
    // Check for items with quantity 0 or less
    $cart_has_invalid_item = false;
    foreach ($_SESSION['cart'] as $cart_item_check_qty) {
        if (intval($cart_item_check_qty['qty']) <= 0) {
            $cart_has_invalid_item = true;
            break;
        }
    }

    if ($cart_has_invalid_item) {
        $_SESSION['cart_error_message'] = "One or more items in your cart have an invalid quantity. Please update quantity to 1 or more, or remove the item.";
        header("Location: /digidine/pages/customer/cart.php");
        exit();
    }

    if (!isset($_SESSION['username'])) {
        header("Location: ../../auth/login.php");
        exit();
    }

    // Get data for the order
    $user_id = $_SESSION['user_id']; // Assuming user_id is set in session
    $customer_name = $_SESSION['username']; // Assuming username is stored in session
    $contact_number = 'N/A'; // Optionally update if stored
    $total_price = 0;

    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['qty'];
    }

    $status = 'pending';
    $created_at = date('Y-m-d H:i:s');

    // Insert order into database
    $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, contact_number, total_price, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdss", $user_id, $customer_name, $contact_number, $total_price, $status, $created_at);
    
    if ($stmt->execute()) {
        // Get the last inserted order ID
        $order_id = $stmt->insert_id;

        // Insert order items into the order_items table
        foreach ($_SESSION['cart'] as $item) {
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity) VALUES (?, ?, ?)");
            $item_stmt->bind_param("iii", $order_id, $item['id'], $item['qty']);
            $item_stmt->execute();
        }

        // Clear cart after successful order placement
        $_SESSION['cart'] = [];

        // Redirect to order confirmation page with success message
        header("Location: /digidine/pages/customer/customer_orders.php?success=1");
        exit();
    } else {
        echo "<div class='text-danger text-center'>❌ Failed to place order.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
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

        .cart-item {
            background: #1e1e1e;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
        }

        .cart-item:hover {
            transform: scale(1.02);
            background: #292929;
            box-shadow: 0 0 20px 5px rgba(255, 102, 0, 0.5);
        }

        .btn-orange {
            background-color: #ff6600;
            border: none;
            color: white;
            border-radius: 30px;
            font-weight: bold;
            padding: 8px 16px;
            transition: background-color 0.3s ease;
        }

        .btn-orange:hover {
            background-color: #e65500;
        }

        .btn-danger {
            border-radius: 30px;
        }

        input[type="number"] {
            width: 80px;
            border-radius: 10px;
            padding: 5px;
        }

        .total-box {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(255, 102, 0, 0.5);
            text-align: right;
            font-size: 1.3rem;
            color: #fff;
        }

        .back-btn {
            margin-top: 30px;
            text-align: center;
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
    <?php
    if (isset($_SESSION['cart_error_message'])) {
        echo "<div class='alert alert-danger text-center' role='alert'>" . htmlspecialchars($_SESSION['cart_error_message']) . "</div>";
        unset($_SESSION['cart_error_message']); // Clear message after displaying
    }
    ?>
    <h2>🛒 Your Cart</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
        <?php $total = 0; ?>
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <div class="cart-item">
                <h4><?php echo $item['name']; ?></h4>
                <p>Price: ₹<?php echo $item['price']; ?> | Quantity: <?php echo $item['qty']; ?></p>
                <p><strong>Subtotal:</strong> ₹<?php echo $item['price'] * $item['qty']; ?></p>

                <form method="post" action="/digidine/pages/customer/cart.php" class="d-inline-block" onsubmit="return confirmUpdateQuantity(this);">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <input type="number" name="item_qty" value="<?php echo $item['qty']; ?>" min="0" step="1">
                    <button type="submit" name="update_qty" class="btn btn-sm btn-orange">Update</button>
                </form>

                <form method="post" action="/digidine/pages/customer/cart.php" class="d-inline-block">
                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                    <button type="submit" name="remove_item" class="btn btn-sm btn-danger">Remove</button>
                </form>
            </div>
            <?php $total += $item['price'] * $item['qty']; ?>
        <?php endforeach; ?>

        <div class="total-box mt-4">
            <strong>Total: ₹<?php echo $total; ?></strong>
        </div>
        <form method="post" action="/digidine/pages/customer/cart.php" class="text-center mt-4" onsubmit="return validateBeforeOrder();">
            <input type="submit" name="place_order" class="btn btn-orange" value="🧾 Place Order">
        </form>

        <div class="back-btn">
            <a href="/digidine/pages/customer/menu.php">🍽️ Continue Ordering</a>
        </div>

    <?php else: ?>
        <div class="text-center mt-5">
            <p>Your cart is empty 😞</p>
            <a href="/digidine/pages/customer/menu.php" class="btn btn-orange">Go to Menu</a>
        </div>
    <?php endif; ?>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let pendingForm = null;

    // Called on form submit
    window.confirmUpdateQuantity = function (form) {
        const quantityInput = form.querySelector('input[name="item_qty"]');
        const newQuantity = parseInt(quantityInput.value, 10);
        const originalQuantity = parseInt(quantityInput.defaultValue, 10);

        if (isNaN(newQuantity)) {
            alert("Please enter a valid quantity.");
            quantityInput.value = originalQuantity;
            return false;
        }

        if (newQuantity === originalQuantity) {
            return false; // No change
        }

        if (newQuantity === 0) {
            pendingForm = form;
            document.getElementById('confirmModal').style.display = 'flex';
            return false; // Show modal instead of submitting
        }

        return true; // Normal submit for other quantities
    };

    // YES: Confirm removal
    document.getElementById('confirmYes').addEventListener('click', function () {
        if (pendingForm) {
            document.getElementById('confirmModal').style.display = 'none';

            // Inject hidden input to trigger update_qty PHP logic
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'update_qty';
            hidden.value = '1'; // Any value; just to pass `isset($_POST['update_qty'])`
            pendingForm.appendChild(hidden);

            pendingForm.submit();
        }
    });

    // NO: Cancel removal
    document.getElementById('confirmNo').addEventListener('click', function () {
        if (pendingForm) {
            const qtyInput = pendingForm.querySelector('input[name="item_qty"]');
            qtyInput.value = qtyInput.defaultValue;
            document.getElementById('confirmModal').style.display = 'none';
            pendingForm = null;
        }
    });
});

function validateBeforeOrder() {
    const qtyInputs = document.querySelectorAll('input[name="item_qty"]');
    for (let input of qtyInputs) {
        const qty = parseInt(input.value, 10);
        if (isNaN(qty) || qty <= 0) {
            alert("❌ One or more items have quantity 0. Please update or remove them before placing the order.");
            return false;
        }
    }
    return true;
}
</script>


<!-- Custom Confirmation Modal -->
<div id="confirmModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#1e1e1e; padding:30px; border-radius:20px; max-width:400px; text-align:center; box-shadow:0 0 15px rgba(255,102,0,0.6);">
        <p style="font-size:1.1rem; color:#fff;">⚠️ Do you really want to remove this item from your cart?</p>
        <div style="margin-top:20px;">
            <button id="confirmYes" class="btn btn-orange" style="margin-right:10px;">Yes, Remove</button>
            <button id="confirmNo" class="btn btn-danger">Cancel</button>
        </div>
    </div>
</div>

</body>
</html>
