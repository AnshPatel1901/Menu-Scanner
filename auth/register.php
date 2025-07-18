<?php
session_start(); // Start session for storing messages

include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Check if the username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Store error message in session
        $_SESSION['error_message'] = "Username already exists. Please choose a different one.";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password_hash, $role);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: /Menu scanner/auth/login.php");
            exit(); // Stop script execution after redirect
        } else {
            // Store error message if database query fails
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #212529; /* Dark background for the page */
            color: #ddd; /* Light text for contrast */
            font-family: 'Poppins', sans-serif;
        }
        .container {
            padding-top: 30px;
        }
        h2 {
            color: #fff; /* White color for the header */
            text-align: center;
        }
        .card {
            background-color: #343a40; /* Dark card background */
            border: 1px solid #495057; /* Subtle border */
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out; /* Smooth animation for card hover */
        }
        .card-header {
            background-color: #495057; /* Slightly lighter shade for card header */
            color: #fff;
            font-weight: bold;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body {
            background-color: #343a40;
            color: #ddd;
        }
        .form-control {
            background-color: #495057;
            color: #fff;
            border: 1px solid #6c757d;
            border-radius: 5px;
            transition: background-color 0.3s ease;
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
        .row {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h2>Create an Account</h2>
                </div>
                <div class="card-body">
                    <!-- Display error message if set in session -->
                    <?php
                    if (isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                        unset($_SESSION['error_message']); // Clear the error message after displaying
                    }
                    ?>
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role:</label>
                            <select class="form-control" id="role" name="role">
                                <option value="customer">Customer</option> <!-- Added customer role -->
                                <option value="chef">Chef</option>
                                <option value="manager">Manager</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark btn-block">Register</button>
                    </form>
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="/Menu scanner/auth/login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
