<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Start a session to store admin data after login
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to fetch admin data by email
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        //ChawNadi@2003!
        $hash_code = password_hash($password, PASSWORD_BCRYPT);
        echo "<br>" . $hash_code;
        echo "<br>" . strlen($hash_code);
        // Verify the password
        /* The line `if ( === ['password']) {` is checking if the plain text password
        provided by the user matches the hashed password stored in the database for the admin
        account. */
        // if ($password === $admin['password']) {
            if (password_verify($password, $admin['password'])) {
            // Store admin data in session
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['username'] = $admin['username'];
            $_SESSION['email'] = $admin['email'];

            echo "Login successful! Welcome, " . $admin['username'] . ".";
            // Redirect to admin dashboard
            $_SESSION['admin_logged_in']=true;
            header("Location: admin_index.php");
            exit;
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "No admin found with this email.";
    }

    // Close statement
    $stmt->close();
}

// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>

<body>
    <h1>Admin Login</h1>
    <form action="admin_login.php" method="POST">
        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>

</html>