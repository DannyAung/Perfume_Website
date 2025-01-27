<?php

session_start();
require_once 'db_connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare the SQL query 
$query = "DELETE FROM wishlist WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

// Execute the query
if ($stmt->execute()) {
   
    header("Location: favorite.php");
    exit;
} else {
    echo "Error clearing wishlist.";
}
?>
