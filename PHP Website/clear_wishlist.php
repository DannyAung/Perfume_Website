<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        DELETE FROM wishlist
        WHERE user_id = :user_id
    ");
    $stmt->execute([':user_id' => $user_id]);

    header("Location: favorite.php");
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>