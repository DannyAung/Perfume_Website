<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
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
    $user_id = $_SESSION['user_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO chats (user_id, message, sender, sent_at) VALUES (:user_id, :message, 'user', NOW())");
        $stmt->execute([
            ':user_id' => $user_id,
            ':message' => $message,
        ]);
 header("Location: contact_us.php?page=chatSection");
 exit;
    }
}
?>
