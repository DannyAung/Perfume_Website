<?php

session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


if (isset($_POST['wishlist_id'])) {
    $wishlist_id = intval($_POST['wishlist_id']);


    $query = "DELETE FROM wishlist WHERE wishlist_id = :wishlist_id AND user_id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':wishlist_id', $wishlist_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);


    if ($stmt->execute()) {

        header("Location: favorite.php");
        exit;
    } else {

        echo "Error removing item from wishlist.";
    }
} else {
    header("Location: favorite.php");
    exit;
}
