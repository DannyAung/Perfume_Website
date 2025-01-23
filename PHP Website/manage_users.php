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

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
    $sql = "SELECT * FROM users WHERE 
            user_name LIKE ? OR 
            email LIKE ? OR 
            phone_number LIKE ? OR 
            address LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Fetch users
    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);

    // Delete related records from the reviews table
    $delete_reviews_query = "DELETE FROM reviews WHERE user_id = ?";
    $stmt = $conn->prepare($delete_reviews_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete related records from the order_items table
    $delete_order_items_query = "DELETE FROM order_items WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = ?)";
    $stmt = $conn->prepare($delete_order_items_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete related records from the orders table
    $delete_orders_query = "DELETE FROM orders WHERE user_id = ?";
    $stmt = $conn->prepare($delete_orders_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Delete related records from the cart_items table
    $delete_cart_items_sql = "DELETE FROM cart_items WHERE user_id = ?";
    $stmt = $conn->prepare($delete_cart_items_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();


    // Delete user
    $delete_user_sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($delete_user_sql);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $message = "User deleted successfully.";
    } else {
        $message = "Failed to delete the user: " . $conn->error;
    }
    $stmt->close();
    header("Location: manage_users.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Users</title>
    <!-- Bootstrap CSS -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9fafc;
        }

        .container {
            margin-top: 50px;

        }

        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background-color: rgb(65, 98, 228);
            color: white;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #138496;
        }

        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #888;
        }

        .user-table {
            margin-top: 30px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #fff;
            overflow: hidden;
        }

        .user-table th {
            background-color: rgb(46, 94, 146);
            color: #fff;
        }

        .user-table td {
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .user-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>

    <?php include 'offcanvas_sidebar.php'; ?>

    

        <!-- Content -->
        <div class="container my-5">
            <h1 class="text-center mb-2">Manage Users</h1>
            <!-- Search Form -->
    <div class="container mt-5">
        <form action="manage_users.php" method="GET" class="d-flex mb-4">
            <input class="form-control me-2" type="search" name="search" placeholder="Search users" aria-label="Search" value="<?= htmlspecialchars($search_query); ?>">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle user-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['user_id']); ?></td>
                                    <td>
                                        <?php if (!empty($user['user_image'])): ?>
                                            <img src="uploads/profile_images/<?= htmlspecialchars($user['user_image']); ?>" alt="Profile Picture" class="rounded-circle user-image">
                                        <?php else: ?>
                                            <img src="uploads/profile_images/default.png" alt="Default Profile Picture" class="rounded-circle user-image">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['user_name']); ?></td>
                                    <td><?= htmlspecialchars($user['email']); ?></td>
                                    <td><?= htmlspecialchars($user['phone_number']); ?></td>
                                    <td><?= nl2br(htmlspecialchars($user['address'])); ?></td>
                                    <td>
                                        <a href="view_user_details.php?id=<?= $user['user_id']; ?>" class="btn btn-info btn-sm">View Details</a>
                                        <form action="manage_users.php" method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="no-data">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>