<?php
session_start();


if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}


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


if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

 
    $stmt = $pdo->prepare("DELETE FROM chats WHERE user_id = :user_id");
    $stmt->execute([
        ':user_id' => $user_id
    ]);

   
    header("Location: admin_chat.php?message=Chat deleted successfully");
    exit;
} else {
    header("Location: admin_chat.php");
    exit;
}
?>
