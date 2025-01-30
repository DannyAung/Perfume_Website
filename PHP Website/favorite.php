<?php
session_start();
require_once 'db_connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

$user_id = $_SESSION['user_id'];

//Fetch and retrieves wishlist items
$query = "
    SELECT 
        w.wishlist_id, 
        w.date_added, 
        p.product_id, 
        p.product_name, 
        p.image, 
        p.price, 
        p.discounted_price, 
        p.stock_quantity
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    WHERE w.user_id = :user_id
";

$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

//store in wishlist_items
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);

    $query = "SELECT * FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
    //prevent SQL injection
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    if (isset($_POST['add_to_wishlist'])) {
        $query = "SELECT 1 FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
        //prevent SQL injection
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $insert_query = "INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $insert_stmt->execute();
            echo "Item added to wishlist.";
        } else {
            echo "Item already in wishlist.";
        }
    } elseif (isset($_POST['remove_from_wishlist'])) {
        $delete_query = "DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $delete_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $delete_stmt->execute();
        echo "Item removed from wishlist.";
    }

    $referer = isset($_SERVER['HTTP_REFERER']) ? filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_URL) : 'user_index.php';
    header("Location: " . $referer);
    exit;
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
        }

        .btn-clear {
            background-color: #dc3545;
        }
        .btn-clear:hover {
            background-color:rgb(17, 16, 16);
            color: #fff; /* Change the text color to white */
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
            color:black; 
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
                                <th>Date Added</th>
                                <th>Stock Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($wishlist_items)) : ?>
                                <?php foreach ($wishlist_items as $item) : ?>
                                    <tr>

                                        <td class="text-center">
                                            <form method="POST" action="remove_from_wishlist.php">
                                                <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                                                <button type="submit" class="btn  btn-outline-danger">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </td>


                                        <td class="d-flex align-items-center">
                                            <img src="products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="img-thumbnail me-3" style="width: 70px; height: 70px; object-fit: cover;">
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                            </div>
                                        </td>


                                        <td>$<?php echo number_format($item['price'], 2); ?></td>


                                        <td>
                                            <?php if (!empty($item['discounted_price'])) : ?>
                                                <span class="text-success">$<?php echo number_format($item['discounted_price'], 2); ?></span>
                                            <?php else : ?>
                                                <span class="text-muted">No discount</span>
                                            <?php endif; ?>
                                        </td>


                                        <td><?php echo date("d F Y", strtotime($item['date_added'])); ?></td>


                                        <td>
                                            <?php if ($item['stock_quantity'] > 0) : ?>
                                                <span class="badge bg-success">In stock</span>
                                            <?php else : ?>
                                                <span class="badge bg-danger">Out of stock</span>
                                            <?php endif; ?>
                                        </td>


                                        <td class="text-center">
                                            <form method="POST" action="add_to_cart1.php">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
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


                <div class="d-flex justify-content-between mt-3">
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

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

</body>

</html>