<?php
require_once 'db_connection.php';
session_start();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    try {
        $sql = "DELETE FROM users WHERE user_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $user_id]);

        $_SESSION['success'] = 'User deleted successfully!';
        header('Location: manage_users.php');
        exit;
    } catch (PDOException $e) {
        echo 'Error deleting user: ' . $e->getMessage();
    }
}
?>
