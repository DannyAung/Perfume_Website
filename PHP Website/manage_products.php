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

// Fetch products
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="./images/Logo.png" alt="Logo" style="width:50px;">
                <b>ADMIN DASHBOARD</b>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="view_reports.php">Reports</a></li>
                </ul>
                <a href="admin_login.php" class="btn btn-outline-dark">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-4">Manage Products</h1>
        <a href="add_product.php" class="btn btn-success mb-3">Add New Product</a>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock Quantity</th>
                        <th>Category</th>
                        <th>Sub-Category</th>
                        <th>Size</th>
                        <th>Discount_Available</th>
                        <th>Discount_Percentage</th>
                        <th>Discounted_Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['product_id']); ?></td>
                        <td><?= htmlspecialchars($product['product_name']); ?></td>
                        <td><img src="products/<?= htmlspecialchars($product['image']); ?>" alt="Product Image" style="width:43px; height:55px;"></td>
                        <td><?= nl2br(htmlspecialchars($product['description'])); ?></td>
                        <td><?= number_format($product['price'], 2); ?></td>
                        <td><?= $product['stock_quantity']; ?></td>
                        <td><?= htmlspecialchars($product['category']); ?></td>
                        <td><?= htmlspecialchars($product['subcategory'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($product['size']); ?></td>
                        <td><?= htmlspecialchars($product['discount_available']); ?></td>
                        <td>
                            <?php if ($product['discount_available'] == 'Yes'): ?>
                                <?= htmlspecialchars($product['discount_percentage']); ?>%
                            <?php else: ?>
                                0%
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['discount_available'] == 'Yes'): ?>
                                <?= number_format($product['discounted_price'], 2); ?>
                            <?php else: ?>
                                0.00
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_products.php?id=<?= $product['product_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_product.php?id=<?= $product['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="12">No products found.</td></tr>
            <?php endif; ?>
            </tbody>

            </table>
        </div>
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
