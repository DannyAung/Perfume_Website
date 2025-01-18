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

// Check if a user_id is passed
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Delete all chats for this user
    $stmt = $pdo->prepare("DELETE FROM chats WHERE user_id = :user_id");
    $stmt->execute([
        ':user_id' => $user_id
    ]);

    // Redirect back to the admin chat page with a success message
    header("Location: admin_chat.php?message=Chat deleted successfully");
    exit;
} else {
    // Redirect to the chat page if no user_id is provided
    header("Location: admin_chat.php");
    exit;
}
?>
