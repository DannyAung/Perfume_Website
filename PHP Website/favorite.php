<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

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
        body {
            overflow-x: hidden;
        }

        .wishlist-page {
            padding-bottom: 40px;
        }

        .wishlist-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
        }

        .wishlist-table img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-cell {
            min-width: 220px;
        }

        .product-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-title {
            font-weight: 600;
            line-height: 1.35;
            word-break: break-word;
        }

        .btn-clear,
        .btn-add-all {
            text-decoration: none;
            color: #fff;
            padding: 9px 14px;
            border-radius: 8px;
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

        .btn-add-all:hover {
            color: #fff;
            opacity: 0.95;
        }

        .btn-outline-danger:hover {
            color: black;
        }

        .bottom-actions {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 16px;
        }

        @media (max-width: 991.98px) {
            .wishlist-banner h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 767.98px) {
            .wishlist-banner h1 {
                font-size: 1.7rem;
            }

            .wishlist-banner p {
                font-size: 0.95rem;
            }

            .product-wrap {
                align-items: flex-start;
            }

            .product-wrap img {
                width: 60px;
                height: 60px;
            }

            .table th,
            .table td {
                font-size: 0.92rem;
                vertical-align: middle;
            }

            .bottom-actions {
                flex-direction: column;
            }

            .bottom-actions form,
            .bottom-actions button {
                width: 100%;
            }

            .btn-clear,
            .btn-add-all {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 575.98px) {
            .wishlist-container {
                padding-left: 10px;
                padding-right: 10px;
            }

            .wishlist-banner h1 {
                font-size: 1.5rem;
            }

            .table th,
            .table td {
                font-size: 0.88rem;
                padding: 10px 8px;
            }

            .product-wrap {
                min-width: 180px;
            }

            .product-wrap img {
                width: 55px;
                height: 55px;
            }

            .action-btn-text {
                display: none;
            }
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

    <div class="wishlist-page">
        <div class="wishlist-container container my-4">
            <div class="wishlist-banner text-center mb-4">
                <h1 class="mt-2">Your Wishlist</h1>
                <p class="text-muted mb-0">Your favorite items are just a click away!</p>
            </div>

            <div class="card shadow-lg wishlist-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle wishlist-table mb-0">
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
                                        <?php
                                        $rating = (float)($item['avg_rating'] ?? 0);
                                        $image = !empty($item['image']) ? $item['image'] : 'default-image.jpg';
                                        $product_name = htmlspecialchars($item['product_name'] ?? 'Unknown Product');
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <form method="POST" action="favorite.php">
                                                    <input type="hidden" name="product_id" value="<?php echo (int)$item['product_id']; ?>">
                                                    <button type="submit" name="remove_from_wishlist" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </td>

                                            <td class="product-cell">
                                                <div class="product-wrap">
                                                    <img src="products/<?php echo htmlspecialchars($image); ?>"
                                                         alt="<?php echo $product_name; ?>"
                                                         class="img-thumbnail">
                                                    <div class="product-title">
                                                        <?php echo $product_name; ?>
                                                    </div>
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
                                                        <i class="bi bi-cart-plus"></i>
                                                        <span class="action-btn-text"> Add to Cart</span>
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
                </div>

                <div class="bottom-actions">
                    <form method="POST" action="clear_wishlist.php">
                        <button type="submit" class="btn-clear">Clear All</button>
                    </form>

                    <form method="POST" action="add_all_to_cart.php">
                        <button type="submit" class="btn-add-all">Add All to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>