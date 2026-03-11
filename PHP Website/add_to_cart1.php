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

    try {
        $check_stmt = $pdo->prepare("
            SELECT quantity
            FROM cart_items
            WHERE user_id = :user_id AND product_id = :product_id
        ");
        $check_stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);

        $existing_item = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_item) {
            $new_quantity = (int)$existing_item['quantity'] + $quantity;

            $update_stmt = $pdo->prepare("
                UPDATE cart_items
                SET quantity = :quantity
                WHERE user_id = :user_id AND product_id = :product_id
            ");
            $update_stmt->execute([
                ':quantity' => $new_quantity,
                ':user_id' => $user_id,
                ':product_id' => $product_id
            ]);
        } else {
            $insert_stmt = $pdo->prepare("
                INSERT INTO cart_items (user_id, product_id, quantity)
                VALUES (:user_id, :product_id, :quantity)
            ");
            $insert_stmt->execute([
                ':user_id' => $user_id,
                ':product_id' => $product_id,
                ':quantity' => $quantity
            ]);
        }

        header("Location: add_to_cart.php");
        exit;

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

header("Location: add_to_cart.php");
exit;
?>