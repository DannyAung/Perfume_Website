<?php
session_start();
require_once 'db_connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all wishlist items 
$query = "
    SELECT w.product_id, p.stock_quantity
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    WHERE w.user_id = :user_id
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($wishlist_items)) {
    foreach ($wishlist_items as $item) {
        $product_id = $item['product_id'];
        $stock_quantity = $item['stock_quantity'];
        $quantity = 1;
        
        $cart_query = "SELECT * FROM cart_items WHERE user_id = :user_id AND product_id = :product_id AND ordered_status = 'not_ordered'";
        $cart_stmt = $conn->prepare($cart_query);
        $cart_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $cart_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $cart_stmt->execute();

        if ($cart_stmt->rowCount() > 0) {

            $cart_item = $cart_stmt->fetch(PDO::FETCH_ASSOC);
            $new_quantity = $cart_item['quantity'] + $quantity;


            $update_query = "UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
            $update_stmt->bindParam(':cart_item_id', $cart_item['cart_item_id'], PDO::PARAM_INT);
            $update_stmt->execute();
        } else {

            $insert_query = "INSERT INTO cart_items (user_id, product_id, quantity, ordered_status) VALUES (:user_id, :product_id, :quantity, 'not_ordered')";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $insert_stmt->execute();
        }
    }


    $clear_query = "DELETE FROM wishlist WHERE user_id = :user_id";
    $clear_stmt = $conn->prepare($clear_query);
    $clear_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $clear_stmt->execute();


    header("Location: add_to_cart.php");
    exit;
} else {
    echo "Your wishlist is empty.";
    exit;
}
