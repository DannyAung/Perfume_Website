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
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<style>

/*Admin Nav*/
.custom-navbar-spacing .nav-item {
    margin-right: -15px;
   
}

.custom-navbar-spacing .nav-link {
    padding-left: 25px;
   
    padding-right: 5px;
   
}
.hover-bg:hover {
        background-color: rgba(0, 123, 255, 0.1); /* Light blue hover effect */
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
</style>
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
                <a href="logout.php" class="btn btn-outline-dark">Logout</a>
            </div>
        </div>
    </nav>
    <br>
    <!-- Rest of your content -->
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
                <a class="nav-link d-flex align-items-center p-3 hover-bg" href="view_reports.php">
                    <i class="bi bi-bar-chart me-3 fs-5"></i>
                    <span class="fs-6">Reports</span>
                </a>
            </li>
        </ul>
    </div>
</div>




  
    <div class="container my-5">
    <h1 class="text-center mb-4">Welcome, Admin!</h1>
    <div class="row g-4">
        <!-- Manage Products -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-boxes fa-3x mb-3 text-primary"></i>
                    <h5 class="card-title mb-2">Manage Products</h5>
                    <p class="card-text mb-3">Add, update, or delete products.</p>
                    <a href="manage_products.php" class="btn btn-outline-primary w-100">Go</a>
                </div>
            </div>
        </div>

        <!-- Manage Orders -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-shopping-cart fa-3x mb-3 text-success"></i>
                    <h5 class="card-title mb-2">Manage Orders</h5>
                    <p class="card-text mb-3">View and update order status.</p>
                    <a href="manage_orders.php" class="btn btn-outline-success w-100">Go</a>
                </div>
            </div>
        </div>

        <!-- Manage Coupons -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-tags fa-3x mb-3 text-warning"></i>
                    <h5 class="card-title mb-2">Manage Coupons</h5>
                    <p class="card-text mb-3">View and update coupons.</p>
                    <a href="manage_coupon.php" class="btn btn-outline-warning w-100">Go</a>
                </div>
            </div>
        </div>

        <!-- Manage Users -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-users fa-3x mb-3 text-info"></i>
                    <h5 class="card-title mb-2">Manage Users</h5>
                    <p class="card-text mb-3">View or manage users.</p>
                    <a href="manage_users.php" class="btn btn-outline-info w-100">Go</a>
                </div>
            </div>
        </div>

        <!-- Manage Reviews -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-star fa-3x mb-3 text-secondary"></i>
                    <h5 class="card-title mb-2">Manage Reviews</h5>
                    <p class="card-text mb-3">View or manage reviews.</p>
                    <a href="manage_reviews.php" class="btn btn-outline-secondary w-100">Go</a>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-chart-line fa-3x mb-3 text-danger"></i>
                    <h5 class="card-title mb-2">Reports</h5>
                    <p class="card-text mb-3">View sales and performance reports.</p>
                    <a href="view_reports.php" class="btn btn-outline-danger w-100">Go</a>
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
