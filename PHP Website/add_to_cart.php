<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_cart'])) {
            $product_id = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);

            if ($product_id > 0) {
                if ($quantity <= 0) {
                    $delete_stmt = $pdo->prepare("
                        DELETE FROM cart_items
                        WHERE user_id = :user_id AND product_id = :product_id
                    ");
                    $delete_stmt->execute([
                        ':user_id' => $user_id,
                        ':product_id' => $product_id
                    ]);
                } else {
                    $update_stmt = $pdo->prepare("
                        UPDATE cart_items
                        SET quantity = :quantity
                        WHERE user_id = :user_id AND product_id = :product_id
                    ");
                    $update_stmt->execute([
                        ':quantity' => $quantity,
                        ':user_id' => $user_id,
                        ':product_id' => $product_id
                    ]);
                }
            }

            header("Location: add_to_cart.php");
            exit;
        }

        if (isset($_POST['remove_item'])) {
            $product_id = (int)($_POST['product_id'] ?? 0);

            if ($product_id > 0) {
                $delete_stmt = $pdo->prepare("
                    DELETE FROM cart_items
                    WHERE user_id = :user_id AND product_id = :product_id
                ");
                $delete_stmt->execute([
                    ':user_id' => $user_id,
                    ':product_id' => $product_id
                ]);
            }

            header("Location: add_to_cart.php");
            exit;
        }

        if (isset($_POST['clear_cart'])) {
            $clear_stmt = $pdo->prepare("
                DELETE FROM cart_items
                WHERE user_id = :user_id
            ");
            $clear_stmt->execute([':user_id' => $user_id]);

            header("Location: add_to_cart.php");
            exit;
        }
    }

    $stmt = $pdo->prepare("
        SELECT 
            c.product_id,
            c.quantity,
            p.product_name,
            p.image,
            p.price,
            p.discounted_price,
            p.stock_quantity,
            p.subcategory
        FROM cart_items c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = :user_id
        ORDER BY c.product_id DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grand_total = 0;
    foreach ($cart_items as $item) {
        $unit_price = (
            isset($item['subcategory'], $item['discounted_price'], $item['price']) &&
            $item['subcategory'] === 'discount' &&
            (float)$item['discounted_price'] > 0 &&
            (float)$item['discounted_price'] < (float)$item['price']
        ) ? (float)$item['discounted_price'] : (float)$item['price'];

        $grand_total += $unit_price * (int)$item['quantity'];
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Cart</li>
            </ol>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4">My Cart</h2>

        <?php if (!empty($cart_items)): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th width="170">Quantity</th>
                            <th>Total</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <?php
                            $unit_price = (
                                isset($item['subcategory'], $item['discounted_price'], $item['price']) &&
                                $item['subcategory'] === 'discount' &&
                                (float)$item['discounted_price'] > 0 &&
                                (float)$item['discounted_price'] < (float)$item['price']
                            ) ? (float)$item['discounted_price'] : (float)$item['price'];

                            $line_total = $unit_price * (int)$item['quantity'];
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="products/<?php echo htmlspecialchars($item['image']); ?>"
                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                             style="width: 70px; height: 70px; object-fit: cover;"
                                             class="me-3 rounded">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                        </div>
                                    </div>
                                </td>

                                <td>$<?php echo number_format($unit_price, 2); ?></td>

                                <td>
                                    <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                        <input type="hidden" name="product_id" value="<?php echo (int)$item['product_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo (int)$item['quantity']; ?>" min="0" class="form-control">
                                        <button type="submit" name="update_cart" class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                </td>

                                <td>$<?php echo number_format($line_total, 2); ?></td>

                                <td>
                                    <form method="POST" action="add_to_cart.php">
                                        <input type="hidden" name="product_id" value="<?php echo (int)$item['product_id']; ?>">
                                        <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <form method="POST" action="add_to_cart.php">
                    <button type="submit" name="clear_cart" class="btn btn-outline-danger">Clear Cart</button>
                </form>

                <div class="text-end">
                    <h4>Grand Total: $<?php echo number_format($grand_total, 2); ?></h4>
                    <a href="checkout.php" class="btn btn-success mt-2">Proceed to Checkout</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Your cart is empty.</div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>