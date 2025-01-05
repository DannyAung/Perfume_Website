<?php
// Start session
if (!isset($_SESSION)) {
    session_start(); // Start session if not already started
}

// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men's Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="./images/Logo.png" alt="Logo" style="width:50px; height:auto;">
                <b class="ms-2 dm-serif-display-regular-italic custom-font-color">FRAGRANCE HAVEN</b>
            </a>

            <!-- Toggler Button for Small Screens -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex flex-column flex-lg-row w-100 align-items-center">
                    <!-- Search Bar in the Center -->
                    <div class="mx-auto my-2 my-lg-0">
                        <form class="d-flex">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </div>

                    <!-- Display Username or Guest -->
                    <span class="navbar-text me-3 my-2 my-lg-0">
                        Welcome, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>!
                    </span>

                    <!-- Account Dropdown for Logged-In Users -->
                    <?php if ($is_logged_in): ?>
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Account
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                                <li><a class="dropdown-item" href="user_orders.php">Orders</a></li>
                                <li><a class="dropdown-item" href="user_profile.php">View Profile</a></li>
                                <li><a class="dropdown-item" href="user_logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Login and Cart Buttons on the Right -->
            <div class="d-flex justify-content-center justify-content-lg-end my-2 my-lg-0">
                <?php if (!$is_logged_in): ?>
                    <a href="user_login.php" class="btn login-btn me-3">Login/Register</a>
                <?php endif; ?>
                <a href="add_to_cart.php" class="btn cart-btn" id="cart-button">
                    <img src="./images/cart-icon.jpg" alt="Cart" style="width:20px; height:20px; margin-right:6px;">
                    Cart
                </a>
            </div>
        </div>
    </nav>

  <!-- New Navigation Links Section -->
  <div class="py-1">
        <div class="container">
            <ul class="nav justify-content">
                <li class="nav-item">
                    <a class="nav-link active" href="user_index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">
                        Category
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li><a class="dropdown-item" href="men_category.php">Men</a></li>
                        <li><a class="dropdown-item" href="women_category.php">Women</a></li>
                        <li><a class="dropdown-item" href="unisex_category.php">Unisex</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Delivery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </div>


    <h2 class="mb-4 mt-5">Men's Collection</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php
        // Query to fetch only "Men" category products
        $query = "SELECT * FROM products WHERE category = 'men' ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);

        while ($product = mysqli_fetch_assoc($result)) {
            // Get stock quantity and check if it's sold out
            $stock_quantity = $product['stock_quantity'];
            $is_sold_out = $stock_quantity == 0;

            // Image path logic
            $image = isset($product['image']) && !empty($product['image'])
                ? 'products/' . htmlspecialchars($product['image'])
                : 'images/default-image.jpg';

            // Check if image exists, otherwise fallback
            if (!file_exists($image)) {
                $image = 'images/default-image.jpg';  // Fallback image
            }

            $product_name = htmlspecialchars($product['product_name']);
            $product_price = htmlspecialchars($product['price']);
        ?>
            <div class="col">
                <div class="card h-100 text-center shadow d-flex flex-column">
                    <div class="image-container" style="width: auto; margin: 0 auto;">
                        <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo $product_name; ?>"
                            style="max-height: 150px; width: 100%; object-fit: contain;">
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $product_name; ?></h5>
                        <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>

                        <!-- Sold Out Message and Prevent Add to Cart -->
                        <?php if ($is_sold_out): ?>
                            <p class="text-danger fw-bold">Sold Out</p>
                            <button class="btn btn-outline-secondary btn-sm" disabled>Out of Stock</button>
                        <?php else: ?>
                            <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo $product_name; ?>">
                                <input type="hidden" name="product_price" value="<?php echo $product_price; ?>">
                                <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['image']); ?>">

                                <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    Add to Cart
                                </button>
                                <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-sm flex-grow-1">
                                    View Details
                                </a>
                            </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>