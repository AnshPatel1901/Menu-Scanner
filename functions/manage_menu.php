<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include '../includes/db.php';

// Add Menu Item
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("INSERT INTO menu (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $description, $price, $image);

    if ($stmt->execute()) {
        echo "<script>alert('New menu item added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}

// Delete Menu Item
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM menu WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Menu item deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}

// Edit Menu Item
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("UPDATE menu SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $name, $description, $price, $image, $id);


    if ($stmt->execute()) {
        echo "<script>alert('Menu item updated successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}

$result = $conn->query("SELECT * FROM menu");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu</title>
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
            transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
        }

        .card:hover {
            transform: scale(1.04);
            background: #292929;
            box-shadow: 0 0 25px 5px rgba(255, 102, 0, 0.6);
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

        .btn-primary, .btn-warning, .btn-danger {
            border-radius: 30px;
            font-weight: bold;
            padding: 8px 18px;
        }

        .btn-primary {
            background-color: #ff6600;
            border: none;
        }

        .btn-primary:hover {
            background-color: #e65500;
        }

        .btn-warning {
            background-color: #ffc107;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .container {
            padding-bottom: 50px;
        }

        label {
            color: #cccccc;
        }

        input[type="text"], input[type="number"] {
            background-color: #2c2c2c;
            border: none;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Manage Menu</h2>

        <!-- Add Menu Item Form -->
        <div class="card mb-5">
            <div class="card-body">
                <h4 class="card-title">Add New Menu Item</h4>
                <form method="POST" action="manage_menu.php">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <input type="text" class="form-control" name="description" required>
                    </div>
                    <div class="form-group">
                        <label>Price (₹):</label>
                        <input type="number" class="form-control" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Image URL:</label>
                        <input type="text" class="form-control" name="image" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="add">Add Menu Item</button>
                </form>
            </div>
        </div>

        <!-- Display Existing Menu Items -->
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="/digidine/assets/images/<?php echo basename($row['image']); ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['name']; ?></h5>
                            <p class="card-text"><?php echo $row['description']; ?></p>
                            <p class="card-text"><strong>Price:</strong> ₹<?php echo $row['price']; ?></p>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger mb-2"
                            onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                            <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content" style="background-color: #1e1e1e; color: #ffffff;">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Menu Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="manage_menu.php">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <div class="form-group">
                                        <label>Name:</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo $row['name']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Description:</label>
                                        <input type="text" class="form-control" name="description" value="<?php echo $row['description']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Price (₹):</label>
                                        <input type="number" class="form-control" name="price" value="<?php echo $row['price']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Image URL:</label>
                                        <input type="text" class="form-control" name="image" value="<?php echo $row['image']; ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="edit">Update Menu Item</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Bootstrap JS (for modals) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
