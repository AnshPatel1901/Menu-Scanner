<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Restaurant</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #f0f0f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            text-align: center;
            margin-top: 100px;
        }

        h1 {
            color: #ff6600;
            font-size: 3rem;
            text-shadow: 0 0 10px rgba(255, 102, 0, 0.7);
            margin-bottom: 40px;
        }

        .btn-primary {
            background-color: #ff6600;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            font-size: 1.2rem;
            margin: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e65500;
        }

        .intro-text {
            color: #cccccc;
            font-size: 1.2rem;
        }

        .card {
            background: #1e1e1e;
            border: none;
            border-radius: 20px;
            margin: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
        }

        .card:hover {
            transform: scale(1.05);
            background: #292929;
            box-shadow: 0 0 20px rgba(255, 102, 0, 0.6);
        }

        .card-img-top {
            height: 250px;
            object-fit: cover;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        .card-title {
            color: #ff6600;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .card-body {
            color: #cccccc;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome to Our Restaurant</h1>
    <p class="intro-text">Explore our delicious menu and start ordering your favorite dishes. Enjoy your meal!</p>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <img src="assets/images/menu.png" class="card-img-top" alt="Menu">
                <div class="card-body">
                    <h5 class="card-title">Explore Our Menu</h5>
                    <p class="card-text">Choose from a variety of dishes, from appetizers to main courses.</p>
                    <a href="pages/customer/menu.php" class="btn btn-primary">Go to Menu</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <img src="assets/images/cart.png" class="card-img-top" alt="Cart">
                <div class="card-body">
                    <h5 class="card-title">Your Cart</h5>
                    <p class="card-text">View the items you've added to your cart and proceed to checkout.</p>
                    <a href="pages/customer/cart.php" class="btn btn-primary">View Cart</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <img src="assets/images/lr.png" class="card-img-top" alt="Login">
                <div class="card-body">
                    <h5 class="card-title">Login/Register</h5>
                    <p class="card-text">Already a member? Log in to track your orders and favorites.</p>
                    <a href="auth/login.php" class="btn btn-primary">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
