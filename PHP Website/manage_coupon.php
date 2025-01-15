<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle Add Coupon request
if (isset($_POST['add_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    $discount_percentage = $_POST['discount_percentage'];
    $valid_from = $_POST['valid_from'];
    $valid_to = $_POST['valid_to'];
    $minimum_purchase_amount = $_POST['minimum_purchase_amount'];

    // Insert coupon into the database
    $query = "INSERT INTO coupons (coupon_code, discount_percentage, valid_from, valid_to, minimum_purchase_amount) 
              VALUES ('$coupon_code', '$discount_percentage', '$valid_from', '$valid_to', '$minimum_purchase_amount')";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Coupon added successfully!';
    } else {
        $_SESSION['error'] = 'Error adding coupon: ' . mysqli_error($conn);
    }
}

// Handle Delete Coupon request
if (isset($_GET['delete_coupon'])) {
    $coupon_id = $_GET['delete_coupon'];
    
    // Delete coupon from database
    $query = "DELETE FROM coupons WHERE coupon_id = $coupon_id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Coupon deleted successfully!';
    } else {
        $_SESSION['error'] = 'Error deleting coupon: ' . mysqli_error($conn);
    }
}

// Fetch all coupons from the database
$query = "SELECT * FROM coupons";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons</title>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9fafc;
            color: #333;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card {
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #fff;
            overflow: hidden;
        }
        .btn-action {
            margin-right: 5px;
        }
        .success-message, .error-message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }


    </style>
</head>
<body>

<!-- Main Content -->
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg">
        <!-- Sidebar Toggle Button -->
        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
            <i class="bi bi-list"></i>
        </button>
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_index.php">
                <img src="./images/perfume_logo.png" alt="Logo" style="width:50px;">
                ADMIN DASHBOARD
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                </ul>
                <a href="admin_login.php" class="btn btn-outline-dark">Logout</a>
            </div>
        </div>
    </nav>
    <br>
</div>

   <!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header bg-light border-bottom">
        <h5 class="offcanvas-title fw-bold" id="sidebarLabel">Admin Dashboard</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="admin_index.php">
                    <i class="bi bi-house-door me-3 fs-5"></i>
                    <span class="fs-6">Home</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="manage_products.php">
                    <i class="bi bi-box me-3 fs-5"></i>
                    <span class="fs-6">Manage Products</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="manage_orders.php">
                    <i class="bi bi-cart me-3 fs-5"></i>
                    <span class="fs-6">Manage Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="manage_coupon.php">
                    <i class="bi bi-tag me-3 fs-5"></i>
                    <span class="fs-6">Manage Coupons</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="manage_users.php">
                    <i class="bi bi-person me-3 fs-5"></i>
                    <span class="fs-6">Manage Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="manage_reviews.php">
                    <i class="bi bi-star me-3 fs-5"></i>
                    <span class="fs-6">Manage Reviews</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="manage_contact_us.php">
                    <i class="bi bi-star me-3 fs-5"></i>
                    <span class="fs-6">Manage Contact</span>
                </a>
            </li>
           
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="view_reports.php">
                    <i class="bi bi-bar-chart me-3 fs-5"></i>
                    <span class="fs-6">Reports</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="admin_chat.php">
                <i class="bi bi-chat me-3 fs-5"></i>
                    <span class="fs-6">Chat With Customer</span>
                </a>
            </li>
        </ul>
    </div>
</div>


    <!-- Main Content -->
    <div class="container mt-4">
    <h1 class="text-center">Manage Coupon</h1>

        <!-- Coupons List -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">Existing Coupons</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Coupon Code</th>
                            <th>Discount (%)</th>
                            <th>Valid From</th>
                            <th>Valid To</th>
                            <th>Minimum Purchase</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($coupon = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($coupon['coupon_code']); ?></td>
                                <td><?php echo $coupon['discount_percentage']; ?>%</td>
                                <td><?php echo $coupon['valid_from']; ?></td>
                                <td><?php echo $coupon['valid_to']; ?></td>
                                <td><?php echo $coupon['minimum_purchase_amount']; ?></td>
                                <td>
                                    <a href="edit_coupon.php?coupon_id=<?php echo $coupon['coupon_id']; ?>" class="btn btn-primary btn-sm btn-action">Edit</a>
                                    <a href="manage_coupon.php?delete_coupon=<?php echo $coupon['coupon_id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this coupon?')" class="btn btn-danger btn-sm btn-action">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Display session messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Add Coupon Form -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Add New Coupon</h4>
            </div>
            <div class="card-body">
                <form action="manage_coupon.php" method="POST">
                    <div class="mb-3">
                        <label for="coupon_code" class="form-label">Coupon Code:</label>
                        <input type="text" name="coupon_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="discount_percentage" class="form-label">Discount Percentage:</label>
                        <input type="number" name="discount_percentage" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="valid_from" class="form-label">Valid From:</label>
                        <input type="date" name="valid_from" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="valid_to" class="form-label">Valid To:</label>
                        <input type="date" name="valid_to" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="minimum_purchase_amount" class="form-label">Minimum Purchase Amount:</label>
                        <input type="number" name="minimum_purchase_amount" class="form-control" step="0.01" required>
                    </div>
                    <button type="submit" name="add_coupon" class="btn btn-primary">Add Coupon</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>