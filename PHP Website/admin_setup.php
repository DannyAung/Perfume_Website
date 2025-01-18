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

// Hash the password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admin (admin_id, username, email, password, created_at, updated_at) 
        VALUES (?, ?, ?, ?, NOW(), NOW())";

$stmt = $conn->prepare($sql);
$admin_id = 1; // Manually specify the admin_id
$stmt->bind_param("isss", $admin_id, $admin_username, $admin_email, $hashed_password);

if ($stmt->execute()) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
