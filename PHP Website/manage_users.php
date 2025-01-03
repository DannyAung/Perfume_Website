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

// Fetch users
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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

    <div class="container my-5">
        <h1 class="text-center mb-4">Manage Users</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>User ID</th>
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
                        <td><?= htmlspecialchars($user['user_name']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['phone_number']); ?></td>
                        <td><?= nl2br(htmlspecialchars($user['address'])); ?></td>
                        <td>
                            <a href="view_user_details.php?id=<?= $user['user_id']; ?>" class="btn btn-info btn-sm">View Details</a>
                            <form action="manage_users.php" method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No users found.</td></tr>
            <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
