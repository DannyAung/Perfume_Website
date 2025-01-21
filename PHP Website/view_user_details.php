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
    <title>Fragrance Haven</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    .navbar {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<body>
<?php include 'admin_navbar.php'; ?>
<?php include 'offcanvas_sidebar.php'; ?>

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
    <?php include 'footer.php'; ?>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

</body>

</html>