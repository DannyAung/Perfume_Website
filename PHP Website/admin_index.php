<?php
// Start session
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}


// Connect to the database
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fragrance Haven</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="./images/Logo.png" alt="Logo" style="width:50px; height:auto;">
                <b class="ms-2 dm-serif-display-regular-italic custom-font-color">ADMIN DASHBOARD</b>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarNav">
            <ul class="navbar-nav me-auto custom-navbar-spacing">
                    <li class="nav-item"><a class="nav-link" href="admin_index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="view_reports.php">Reports</a></li>
                </ul>
                <div class="d-flex ms-auto ">
                    <a href="logout.php" class="btn btn-outline-dark">Logout</a>
                </div>
            </div>
        </div>
    </nav><br>
   
    
   <div class="container my-5">
    <h1 class="text-center mb-4">Welcome, Admin!</h1>
    <div class="row g-4">
        <!-- Manage Products -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Manage Products</h5>
                        <p class="card-text mb-0">Add, update, or delete products.</p>
                    </div>
                    <a href="manage_products.php" class="btn btn-primary ms-2">Go</a>
                </div>
            </div>
        </div>
        
        <!-- Manage Orders -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Manage Orders</h5>
                        <p class="card-text mb-0">View and update order status.</p>
                    </div>
                    <a href="manage_order.php" class="btn btn-primary ms-2">Go</a>
                </div>
            </div>
        </div>

        <!-- Manage Users -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Manage Users</h5>
                        <p class="card-text mb-0">View or manage users.</p>
                    </div>
                    <a href="manage_users.php" class="btn btn-primary ms-2">Go</a>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-1">Reports</h5>
                        <p class="card-text mb-0">View sales and performance reports.</p>
                    </div>
                    <a href="view_reports.php" class="btn btn-primary ms-2">Go</a>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
