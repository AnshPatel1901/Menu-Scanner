<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Check if password matches
        if (password_verify($password, $user['password'])) {
            // Set session variables and log the user in
            $_SESSION['user_id'] = $user['id'];  // Store user ID
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: ../dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            echo "<div class='alert alert-danger'>Incorrect password!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>No user found!</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #212529;
            font-family: 'Poppins', sans-serif;
            color: #ddd;
        }
        .container {
            padding-top: 40px;
        }
        h2 {
            color: #fff;
            text-align: center;
        }
        .card {
            background-color: #343a40;
            border: 1px solid #495057;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: scale(1.01);
        }
        .card-header {
            background-color: #495057;
            color: #fff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body {
            background-color: #343a40;
        }
        .form-control {
            background-color: #495057;
            border: 1px solid #6c757d;
            color: #fff;
            border-radius: 5px;
        }
        .form-control:focus {
            background-color: #6c757d;
            border-color: #ff6600;
        }
        .btn-dark {
            background-color: #ff6600;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-dark:hover {
            background-color: #ff6600;
        }
        .text-center a {
            color: #ff6600;
        }
        .text-center a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Login</h2>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-dark btn-block">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>