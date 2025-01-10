<?php
// Start session and include database connection
session_start();
require_once 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Prepare the SQL query to delete all wishlist items for the logged-in user
$query = "DELETE FROM wishlist WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

// Execute the query to delete the items
if ($stmt->execute()) {
    // Redirect to the wishlist page after clearing
    header("Location: favorite.php");
    exit;
} else {
    // Handle any errors (optional)
    echo "Error clearing wishlist.";
}
?>
