<?php
// Start session
if (!isset($_SESSION)) {
    session_start();
}

// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header("Location: user_login.php"); // Redirect to login page if not logged in
    exit;
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch user details
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>User Profile</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Welcome, <?php echo htmlspecialchars($user['user_name']); ?>!</h5>
                <p class="card-text">
                    <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
                    <strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?><br>
                    <strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?><br>
                    <strong>Member Since:</strong> <?php echo htmlspecialchars($user['created_at']); ?>
                </p>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                <a href="user_index.php" class="btn btn-primary">Go to Home</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
