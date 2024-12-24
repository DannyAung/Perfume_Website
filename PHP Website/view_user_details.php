<?php
// Start session
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get user ID from query parameter
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
} else {
    die("Invalid user ID.");
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="./images/Logo.png" alt="Logo" style="width:50px;">
                <b>ADMIN DASHBOARD</b>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="view_reports.php">Reports</a></li>
                </ul>
                <a href="admin_login.php" class="btn btn-outline-dark">Logout</a>
            </div>
        </div>
    </nav>

    <!-- User Details Section -->
    <div class="container my-5">
        <h1 class="text-center mb-4">User Details</h1>
        <?php if (!empty($user)): ?>
            <div class="card mx-auto" style="max-width: 600px;">
                <div class="card-body">
                    <h5 class="card-title">User Information</h5>
                    <p class="card-text"><strong>User ID:</strong> <?= htmlspecialchars($user['user_id']); ?></p>
                    <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($user['user_name']); ?></p>
                    <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
                    <p class="card-text"><strong>Password:</strong> <?= htmlspecialchars($user['password']); ?></p>
                    <p class="card-text"><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone_number']); ?></p>
                    <p class="card-text"><strong>Address:</strong><br><?= nl2br(htmlspecialchars($user['address'])); ?></p>
                    <a href="manage_users.php" class="btn btn-secondary mt-3">Back to Users</a>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center">User not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
