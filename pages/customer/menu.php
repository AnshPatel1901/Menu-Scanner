<?php
ini_set('session.cookie_lifetime', 0); // Ensure session cookies expire when the browser is closed
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["user_id"])) { // Assuming 'user_id' is set upon login
    header("location: /digidine/auth/login.php");
    exit;
}

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Our Menu</title>
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

        .card {
            background: #1e1e1e;
            border: none;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            /* darker base shadow */
            transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
        }

        .card:hover {
            transform: scale(1.04);
            background: #292929;
            /* subtle lightening on hover */
            box-shadow: 0 0 25px 5px rgba(255, 102, 0, 0.6);
            /* strong glow */
        }


        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .card-title {
            color: #ff6600;
            font-size: 1.4rem;
            font-weight: bold;
        }

        .card-text {
            color: #cccccc;
        }
        .custom-cart-btn {
            background-color: #ff6600;
            border: none;
            border-radius: 30px;
            padding: 12px 24px;
            font-size: 1.1rem;
            font-weight: bold;
            box-shadow: 0 0 10px rgba(255, 102, 0, 0.5);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .custom-cart-btn:hover {
            background-color: #e65500;
            transform: scale(1.05);
        }
        .btn-primary {
            background-color: #ff6600;
            border: none;
            border-radius: 30px;
            padding: 10px 18px;
            font-weight: bold;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e65500;
        }

        .container {
            padding-bottom: 50px;
        }
    </style>
</head>

<body>
    <div class="container">

        <h2>Our Signature Menu</h2>
        <div class="row">
            <?php
            include '../../includes/db.php';
            $result = $conn->query("SELECT * FROM menu");
            while ($row = $result->fetch_assoc()) {
                echo "
    <div class='col-md-4'>
        <div class='card'>
            <img src='/digidine/assets/images/" . basename($row['image']) . "' class='card-img-top' alt='{$row['name']}'>
            <div class='card-body'>
                <h5 class='card-title'>{$row['name']}</h5>
                <p class='card-text'>{$row['description']}</p>
                <p class='card-text'><strong>Price:</strong> ₹{$row['price']}</p>
                <form method='post' action='cart.php'>
                    <input type='hidden' name='item_id' value='{$row['id']}'>
                    <input type='hidden' name='item_name' value='{$row['name']}'>
                    <input type='hidden' name='item_price' value='{$row['price']}'>
                    <input type='number' name='item_qty' value='1' min='1' class='form-control mb-2' style='width: 80px; display: inline-block;'>
                    <button type='submit' name='add_to_cart' class='btn btn-primary'>Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
    ";
            }
            ?>

        </div>
    </div>

    <div class="text-center mt-5">
    <a href="/digidine/pages/customer/cart.php" class="btn btn-primary custom-cart-btn">🛒 View Cart</a>
    </div>
</body>

</html>