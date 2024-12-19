<?php
require_once 'db_connection.php';
session_start();

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    try {
        $sql = "DELETE FROM products WHERE product_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $product_id]);

        $_SESSION['success'] = 'Product deleted successfully!';
        header('Location: manage_products.php');
        exit;
    } catch (PDOException $e) {
        echo 'Error deleting product: ' . $e->getMessage();
    }
}
?>
