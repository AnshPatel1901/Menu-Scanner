<?php
session_start();
include '../includes/db.php';

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // First, check if username already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Username already exists, show popup
        echo "<script>alert('Username already exists!'); window.location.href='/digidine/functions/manage_users.php';</script>";
        exit();
    } else {
        // Username is available, insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);
        $stmt->execute();
        $stmt->close();
        header("Location: /digidine/functions/manage_users.php");
        exit();
    }
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Optional: if password is updated
    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $hashed_password, $role, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
        $stmt->bind_param("ssi", $username, $role, $user_id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: /digidine/functions/manage_users.php");
    exit();
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: /digidine/functions/manage_users.php");
    exit();
}

// Fetch all users
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            margin-bottom: 40px;
            font-size: 3rem;
        }

        h2, h3 {
            color: #ff6600;
            margin-top: 30px;
        }

        .container {
            max-width: 1200px;
            margin-top: 30px;
        }

        .card {
            background-color: #282828;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 20px rgba(255, 102, 0, 0.3);
        }

        .card-header {
            background-color: #333;
            color: #fff;
            font-size: 1.5rem;
            padding: 20px;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        .form-control {
            background-color: #555;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px;
        }

        .form-control:focus {
            background-color: #666;
            border-color: #ff6600;
        }

        .btn-primary, .btn-success, .btn-info, .btn-danger {
            border-radius: 30px;
            font-weight: bold;
            transition: transform 0.3s ease;
            background-color: #ff6600;
            border: none;
        }

        .btn-primary:hover, .btn-success:hover, .btn-info:hover, .btn-danger:hover {
            transform: translateY(-3px);
            background-color: #e65500;
        }

        .table {
            background-color: #333;
            color: #f0f0f0;
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead {
            background-color: #444;
            color: #fff;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        a.btn {
            margin-left: 5px;
        }

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
    <h1>Manage Users</h1>

    <div class="card mb-5">
        <div class="card-header">
            Add New User
        </div>
        <div class="card-body">
            <form method="POST" action="manage_users.php">
                <div class="form-row">
                    <div class="col">
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                    <div class="col">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col">
                        <select name="role" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="manager">Manager</option>
                            <option value="chef">Chef</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>
                    <div class="col">
                        <button type="submit" name="add_user" class="btn btn-success btn-block">Add User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Existing Users
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $users->fetch_assoc()) { ?>
                    <tr>
                        <form method="POST" action="manage_users.php">
                            <td><?php echo $row['id']; ?></td>
                            <td>
                                <input type="text" name="username" value="<?php echo $row['username']; ?>" class="form-control" required>
                            </td>
                            <td>
                                <select name="role" class="form-control">
                                    <option value="admin" <?php if($row['role']=='admin') echo "selected"; ?>>Admin</option>
                                    <option value="manager" <?php if($row['role']=='manager') echo "selected"; ?>>Manager</option>
                                    <option value="chef" <?php if($row['role']=='chef') echo "selected"; ?>>Chef</option>
                                    <option value="customer" <?php if($row['role']=='customer') echo "selected"; ?>>Customer</option>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                <input type="password" name="password" class="form-control mb-2" placeholder="New Password (optional)">
                                <button type="submit" name="update_user" class="btn btn-info btn-sm">Update</button>
                                <a href="manage_users.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</a>
                            </td>
                        </form>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="/digidine/dashboard.php" class="btn btn-primary mb-4">← Back to Dashboard</a>

</div>
</body>
</html>
