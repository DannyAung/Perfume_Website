<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$db_name = 'ecom_website';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4;port=$port", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch all users who have sent messages
$stmt = $pdo->prepare("SELECT DISTINCT user_id, MAX(sent_at) AS last_message_time FROM chats GROUP BY user_id ORDER BY last_message_time DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat</title>
    <title>Admin Dashboard - Manage Users</title>
    <!-- Bootstrap CSS -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<style>
    .navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<body>
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
                        <i class="bi  me-3 fs-5"></i>
                        <span class="fs-6">Chat With Customer</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>


    <div class="container mt-5">
        <h2>Customer Messages</h2>
        <table class="table table-striped">
        <thead class=table-warning>
                <tr>
                    <th>User ID</th>
                    <th>Last Message Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_message_time']); ?></td>
                        <td>
                            <a href="admin_chat_reply.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-primary btn-sm">View Chat</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Bootstrap JS and Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="edit_products.js"></script>
</body>

</html>