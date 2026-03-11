<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_SESSION['user_id'];
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($product_id <= 0) {
        die("Invalid product.");
    }

    if ($quantity <= 0) {
        $quantity = 1;
    }

    $check = $pdo->prepare("SELECT cart_id, quantity FROM cart_items WHERE user_id = :user_id AND product_id = :product_id");
    $check->execute([
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $new_quantity = $existing['quantity'] + $quantity;
        $update = $pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE cart_id = :cart_id");
        $update->execute([
            ':quantity' => $new_quantity,
            ':cart_id' => $existing['cart_id']
        ]);
    } else {
        $insert = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
        $insert->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity
        ]);
    }

    header("Location: add_to_cart.php");
    exit;
}
  else {
    // If no product_id is provided, redirect to the wishlist or another appropriate page
    header("Location: favourite.php");
    exit;
}
?>
