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

// Check if the product_id is set
if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Check if the product is already in the cart
    $query = "SELECT ci.*, p.stock_quantity FROM cart_items ci JOIN products p ON ci.product_id = p.product_id WHERE ci.user_id = :user_id AND ci.product_id = :product_id AND ci.ordered_status = 'not_ordered'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Product is already in the cart, you can update the quantity if needed
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);
        $current_quantity = $cart_item['quantity'];
        $stock_quantity = $cart_item['stock_quantity'];

        if ($current_quantity < $stock_quantity) {
            $new_quantity = $current_quantity + 1; // Increase the quantity by 1

            $update_query = "UPDATE cart_items SET quantity = :quantity WHERE cart_item_id = :cart_item_id";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
            $update_stmt->bindParam(':cart_item_id', $cart_item['cart_item_id'], PDO::PARAM_INT);
            $update_stmt->execute();
        } else {
            echo "<script>alert('Error: Cannot increase quantity. Only $stock_quantity items in stock.');</script>";
        }
    } else {
        // Product is not in the cart, insert it
        $query = "INSERT INTO cart_items (user_id, product_id, quantity, ordered_status, added_at) VALUES (:user_id, :product_id, 1, 'not_ordered', NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Redirect to the cart page or wherever you want
    header("Location: add_to_cart.php");
    exit;
} else {
    // If no product_id is provided, redirect to the wishlist or another appropriate page
    header("Location: favourite.php");
    exit;
}
?>
