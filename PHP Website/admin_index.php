<?php

session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if (!isset($_SESSION['admin_id']) || !$_SESSION['admin_logged_in']) {
    echo "Error: Admin ID not set. Please log in again.";
    header('Location: admin_login.php');
    exit;
}


$admin_id = $_SESSION['admin_id'];


$query = "SELECT COUNT(*) as unread_count FROM chats WHERE admin_id = ? AND unread = TRUE";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$unreadCount = $row['unread_count'];

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $query = "UPDATE chats SET unread = FALSE WHERE admin_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $admin_id, $user_id);
    $stmt->execute();
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
.custom-navbar-spacing .nav-item {
    margin-right: -15px;
   
}

.custom-navbar-spacing .nav-link {
    padding-left: 25px;
   
    padding-right: 5px;
   
}
.navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
.hover-bg:hover {
        background-color: rgba(0, 123, 255, 0.1); /* Light blue hover effect */
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
</style>
<body>

<?php include 'admin_navbar.php'; ?>
    <?php include 'offcanvas_sidebar.php'; ?>

    <div class="container my-5">
    <h1 class="text-center mb-4">Welcome, Admin!</h1>
    <div class="row g-4">
      
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

        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-star fa-3x mb-3 text-secondary"></i>
                    <h5 class="card-title mb-2">Manage Contact</h5>
                    <p class="card-text mb-3">View or manage contacts.</p>
                    <a href="manage_contact_us.php" class="btn btn-outline-secondary w-100">Go</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-star fa-3x mb-3 text-secondary"></i>
                    <h5 class="card-title mb-2">Manage Shipping</h5>
                    <p class="card-text mb-3">View or manage shipping methods.</p>
                    <a href="manage_shipping.php" class="btn btn-outline-secondary w-100">Go</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-star fa-3x mb-3 text-secondary"></i>
                    <h5 class="card-title mb-2">Manage Payment</h5>
                    <p class="card-text mb-3">View or manage payment methods.</p>
                    <a href="manage_payment.php" class="btn btn-outline-secondary w-100">Go</a>
                </div>
            </div>
        </div>
      
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

        <div class="col-md-3">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                    <i class="fas fa-star fa-3x mb-3 text-secondary"></i>
                    <h5 class="card-title mb-2">Chat</h5>
                    <p class="card-text mb-3">Chat with Customers.</p>
                    <a href="admin_chat.php" class="btn btn-outline-secondary w-100">Go</a>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php

mysqli_close($conn);
?>
