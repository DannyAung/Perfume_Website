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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO chats (user_id, message, sender, sent_at) VALUES (:user_id, :message, 'admin', NOW())");
        $stmt->execute([
            ':user_id' => $user_id,
            ':message' => $message,
        ]);


        header("Location: admin_chat_reply.php?user_id=$user_id");
        exit;
    }
}
?>
