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

// Check if the wishlist item ID is provided
if (isset($_POST['wishlist_id'])) {
    $wishlist_id = intval($_POST['wishlist_id']);
    
    // Prepare the SQL query to delete the specific wishlist item
    $query = "DELETE FROM wishlist WHERE wishlist_id = :wishlist_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':wishlist_id', $wishlist_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    
    // Execute the query to delete the item
    if ($stmt->execute()) {
        // Redirect back to the wishlist page after removal
        header("Location: favorite.php");
        exit;
    } else {
        // Handle any errors (optional)
        echo "Error removing item from wishlist.";
    }
} else {
    // If no wishlist_id is provided, redirect to the wishlist page
    header("Location: favorite.php");
    exit;
}
?>
