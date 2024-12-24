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

// New admin data
$admin_username = 'admin';
$admin_password = 'admin12';  // Plain text password

// Hash the password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Insert the new admin data into the database
$sql = "INSERT INTO admin (username, email, password, created_at, updated_at) 
        VALUES ('$admin_username', 'admin@example.com', '$hashed_password', NOW(), NOW())";

if (mysqli_query($conn, $sql)) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

// Close the database connection
mysqli_close($conn);
?>
