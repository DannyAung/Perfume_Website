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
    header("Location: user_login.php"); // Redirect if not logged in
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $address = $_POST['address']; // New address field
    $phone_number = $_POST['phone_number']; // New phone_number field
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate current password
    $query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
        $stored_password_hash = $user_data['password'];

        if (password_verify($password, $stored_password_hash)) {
            // Current password is correct

            // Handle new password validation
            if (!empty($new_password)) {
                if ($new_password === $confirm_password) {
                    $password = password_hash($new_password, PASSWORD_DEFAULT);
                } else {
                    $_SESSION['error'] = "New password and confirm password do not match.";
                    header("Location: edit_profile.php");
                    exit;
                }
            } else {
                // If no new password, keep the old password hash
                $password = $stored_password_hash;
            }

            // Update user details in the database
            $update_query = "UPDATE users SET user_name = ?, email = ?, password = ?, address = ?, phone_number = ? WHERE user_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssssi", $user_name, $email, $password, $address, $phone_number, $user_id);

            if ($stmt->execute()) {
                // Update session variables
                $_SESSION['user_name'] = $user_name;

                // Set success message
                $_SESSION['success'] = "Profile updated successfully.";
                header("Location: user_profile.php");
                exit;
            } else {
                $_SESSION['error'] = "Failed to update profile.";
            }
        } else {
            // Incorrect current password
            $_SESSION['error'] = "Incorrect current password.";
            header("Location: edit_profile.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header("Location: edit_profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Profile</h1>

        <!-- Display error or success messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form action="edit_profile.php" method="POST">
            <!-- User Name -->
            <div class="mb-3">
                <label for="user_name" class="form-label">Name</label>
                <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <!-- Address -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>

            <!-- Phone Number -->
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
            </div>

            <!-- Current Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <!-- New Password -->
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
            </div>

            <!-- Confirm New Password -->
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
