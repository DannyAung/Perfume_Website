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
// Fetch all contact messages from the database
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contact Messages - Fragrance Haven</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<style>
    /*Nav*/
    .custom-navbar-spacing .nav-item {
        margin-right: -15px;

    }

    .custom-navbar-spacing .nav-link {
        padding-left: 25px;

        padding-right: 5px;

    }

    .hover-bg:hover {
        background-color: rgba(0, 123, 255, 0.1);
        /* Light blue hover effect */
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                    <a href="admin_login.php" class="btn btn-outline-dark">Logout</a>
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



    <div class="container py-5">
        <h1 class="text-center mb-4">Manage Contact Messages</h1>

        <!-- Display any success or error messages -->
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <table class="table table-striped table-bordered ">
            <thead class=table-warning>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display each contact message
                $index = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>' . $index++ . '</td>';
                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    echo '<td>' . nl2br(htmlspecialchars($row['message'])) . '</td>';
                    echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                    echo '<td>';
                    echo ' <a href="delete_contact.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this message?\')">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
mysqli_close($conn);
?>