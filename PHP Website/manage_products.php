<?php
// Start session
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
    $sql = "SELECT * FROM products WHERE    
            product_name LIKE ? OR
            description LIKE ? OR 
            category LIKE ? OR 
            subcategory LIKE ? OR 
            size LIKE ? OR 
            price LIKE ? OR 
            stock_quantity LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("sssssss", $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Fetch products
    $sql = "SELECT * FROM products";
    $result = mysqli_query($conn, $sql);
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id']);

    // Delete related records from cart_items table
    $delete_cart_items_query = "DELETE FROM cart_items WHERE product_id = ?";
    $stmt = $conn->prepare($delete_cart_items_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    $delete_reviews_query = "DELETE FROM reviews WHERE product_id = ?";
    $stmt = $conn->prepare($delete_reviews_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    // Delete related records from order_items table
    $delete_order_items_query = "DELETE FROM order_items WHERE product_id = ?";
    $stmt = $conn->prepare($delete_order_items_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    // Now delete the product from the products table
    $delete_product_query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($delete_product_query);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        $message = "Product deleted successfully.";
    } else {
        $message = "Failed to delete the product: " . $conn->error;
    }

    $stmt->close();
    header("Location:manage_products.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .btn-outline-dark:hover {
            background-color: #343a40;
            color: #fff;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-weight: bold;
            color: #333;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        table {

            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);

        }

        table img {
            border-radius: 5px;
        }

        th {
            text-align: center;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .product-table {
            margin-top: 30px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #fff;
            overflow: hidden;
        }

        .product-table th {
            background-color: rgb(46, 94, 146);
            color: #fff;
        }

        .product-table td {
            vertical-align: middle;
            font-size: 0.9rem;
        }

        /*Nav*/
        .custom-navbar-spacing .nav-item {
            margin-right: -15px;

        }

        .custom-navbar-spacing .nav-link {
            padding-left: 25px;

            padding-right: 5px;

        }

        .hover-bg:hover {
            background-color: rgba(0, 123, 255, 0.1);
            /* Light blue hover effect */
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
    </style>

<body>

    <?php include 'admin_navbar.php'; ?>

    <?php include 'offcanvas_sidebar.php'; ?>

   

        <div class="container my-3">
            <h1 class="text-center mb-2">Manage Products</h1>
             <!-- Search Form -->
    <div class="container mt-5">
        <form action="manage_products.php" method="GET" class="d-flex mb-4">
            <input class="form-control me-2" type="search" name="search" placeholder="Search products" aria-label="Search" value="<?= htmlspecialchars($search_query); ?>">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
            <div class="d-flex justify-content-end">
                <a href="add_product.php" class="btn btn-success mb-1">Add New Product</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle product-table">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Category</th>
                            <th>Sub-Category</th>
                            <th>Size</th>
                            <th>Discount</th>
                            <th>Discount %</th>
                            <th>Discounted Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="text-center"><?= htmlspecialchars($product['product_id']); ?></td>
                                    <td><?= htmlspecialchars($product['product_name']); ?></td>
                                    <td class="text-center">
                                        <img src="products/<?= htmlspecialchars($product['image']); ?>" alt="Product Image" style="width:50px; height:60px;">
                                    </td>
                                    <td><?= nl2br(htmlspecialchars($product['description'])); ?></td>
                                    <td class="text-end">$<?= number_format($product['price'], 2); ?></td>
                                    <td class="text-center"><?= $product['stock_quantity']; ?></td>
                                    <td><?= htmlspecialchars($product['category']); ?></td>
                                    <td><?= htmlspecialchars($product['subcategory'] ?? 'N/A'); ?></td>
                                    <td><?= htmlspecialchars($product['size']); ?></td>
                                    <td class="text-center"><?= htmlspecialchars($product['discount_available']); ?></td>
                                    <td class="text-center">
                                        <?= htmlspecialchars($product['discount_available'] == 'Yes' ? $product['discount_percentage'] : '0'); ?>%
                                    </td>
                                    <td class="text-end">
                                        $<?= number_format($product['discount_available'] == 'Yes' ? $product['discounted_price'] : 0, 2); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="edit_products.php?id=<?= $product['product_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                        <form action="manage_products.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="product_id" value="<?= $product['product_id']; ?>">
                                            <button type="submit" name="delete_product" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13" class="text-center">No products found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
mysqli_close($conn);
?>