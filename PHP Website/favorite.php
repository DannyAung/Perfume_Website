<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

/*
Assumption:
- Your table is wishlist, not favorites
- wishlist has at least: user_id, product_id
- products table has: product_id, product_name, image, price, discounted_price, stock_quantity
- reviews table has: product_id, rating
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id > 0) {
        try {
            if (isset($_POST['add_to_wishlist'])) {
                $check_stmt = $pdo->prepare("
                    SELECT 1 
                    FROM wishlist 
                    WHERE user_id = :user_id AND product_id = :product_id
                ");
                $check_stmt->execute([
                    ':user_id' => $user_id,
                    ':product_id' => $product_id
                ]);

                if (!$check_stmt->fetchColumn()) {
                    $insert_stmt = $pdo->prepare("
                        INSERT INTO wishlist (user_id, product_id) 
                        VALUES (:user_id, :product_id)
                    ");
                    $insert_stmt->execute([
                        ':user_id' => $user_id,
                        ':product_id' => $product_id
                    ]);
                }
            }

            if (isset($_POST['remove_from_wishlist'])) {
                $delete_stmt = $pdo->prepare("
                    DELETE FROM wishlist 
                    WHERE user_id = :user_id AND product_id = :product_id
                ");
                $delete_stmt->execute([
                    ':user_id' => $user_id,
                    ':product_id' => $product_id
                ]);
            }

            header("Location: favorite.php");
            exit;
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            w.product_id,
            p.product_name,
            p.image,
            p.price,
            p.discounted_price,
            p.stock_quantity,
            p.category,
            p.subcategory,
            COALESCE(AVG(r.rating), 0) AS avg_rating
        FROM wishlist w
        JOIN products p ON w.product_id = p.product_id
        LEFT JOIN reviews r ON p.product_id = r.product_id
        WHERE w.user_id = :user_id
        GROUP BY 
            w.product_id,
            p.product_name,
            p.image,
            p.price,
            p.discounted_price,
            p.stock_quantity,
            p.category,
            p.subcategory
        ORDER BY w.product_id DESC
    ");
    $stmt->execute([':user_id' => $user_id]);
    $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .wishlist-table {
            width: 100%;
            border-collapse: collapse;
        }

        .wishlist-table th,
        .wishlist-table td {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        .wishlist-table img {
            width: 70px;
            height: auto;
            border-radius: 5px;
        }

        .btn-clear,
        .btn-add-all {
            text-decoration: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            border: none;
        }

        .btn-clear {
            background-color: #dc3545;
        }

        .btn-clear:hover {
            background-color: rgb(17, 16, 16);
            color: #fff;
        }

        .btn-add-all {
            background-color: rgb(22, 81, 208);
        }

        .cart-container {
            width: 80%;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-danger:hover {
            color: black;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
            </ol>
        </div>
    </nav>

    <div class="wishlist-container container my-5">
        <div class="wishlist-banner text-center mb-3">
            <h1 class="mt-4">Your Wishlist</h1>
            <p class="text-muted">Your favorite items are just a click away!</p>
        </div>

        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Remove</th>
                            <th>Product</th>
                            <th>Original Price</th>
                            <th>Discounted Price</th>
                            <th>Rating</th>
                            <th>Stock Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($wishlist_items)) : ?>
                            <?php foreach ($wishlist_items as $item) : ?>
                                <?php $rating = (float)($item['avg_rating'] ?? 0); ?>
                                <tr>
                                    <td class="text-center">
                                        <form method="POST" action="favorite.php">
                                            <input type="hidden" name="product_id" value="<?php echo (int)$item['product_id']; ?>">
                                            <button type="submit" name="remove_from_wishlist" class="btn btn-outline-danger">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </td>

                                    <td class="d-flex align-items-center">
                                        <img src="products/<?php echo htmlspecialchars($item['image']); ?>"
                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                             class="img-thumbnail me-3"
                                             style="width: 70px; height: 70px; object-fit: cover;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                        </div>
                                    </td>

                                    <td>$<?php echo number_format((float)$item['price'], 2); ?></td>

                                    <td>
                                        <?php if (!empty($item['discounted_price']) && (float)$item['discounted_price'] > 0) : ?>
                                            <span class="text-success">$<?php echo number_format((float)$item['discounted_price'], 2); ?></span>
                                        <?php else : ?>
                                            <span class="text-muted">No discount</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span class="text-warning">
                                            <?php for ($i = 0; $i < floor($rating); $i++): ?>★<?php endfor; ?>
                                            <?php for ($i = floor($rating); $i < 5; $i++): ?>☆<?php endfor; ?>
                                        </span>
                                        (<?php echo number_format($rating, 1); ?>)
                                    </td>

                                    <td>
                                        <?php if ((int)$item['stock_quantity'] > 0) : ?>
                                            <span class="badge bg-success">In stock</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger">Out of stock</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <form method="POST" action="add_to_cart1.php">
                                            <input type="hidden" name="product_id" value="<?php echo (int)$item['product_id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Your wishlist is empty.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between mt-3 p-3">
                <form method="POST" action="clear_wishlist.php">
                    <button type="submit" class="btn-clear">Clear All</button>
                </form>
                <form method="POST" action="add_all_to_cart.php">
                    <button type="submit" class="btn-add-all">Add All to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>