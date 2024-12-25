<?php
session_start();  // Start the session to access session variables

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

if (!$is_logged_in) {
    // Redirect to login page or show a message
    echo "You need to log in to view this page.";
    exit;  // Stop further execution if user is not logged in
}

require_once 'db_connection.php';  // Ensure the connection file is correct

// Get the product_id from the URL
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

if ($product_id) {
    // Query to fetch the product based on the product_id
    $sql = "SELECT * FROM products WHERE product_id = :product_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Displaying product details
        $product_name = htmlspecialchars($product['product_name']);
        $price = number_format($product['price'], 2);
        $discounted_price = number_format($product['discounted_price'], 2);
        $description = htmlspecialchars($product['description']);
        $stock_quantity = intval($product['stock_quantity']);
        $category = htmlspecialchars($product['category']);
        $subcategory = htmlspecialchars($product['subcategory']);
    } else {
        echo "Product not found.";
    }

    // Query to fetch reviews for the product
    $reviews_sql = "SELECT r.review_text, r.rating, r.created_at, u.user_name FROM reviews r
                    JOIN users u ON r.user_id = u.user_id
                    WHERE r.product_id = :product_id ORDER BY r.created_at DESC";
    $reviews_stmt = $conn->prepare($reviews_sql);
    $reviews_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $reviews_stmt->execute();
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

} else {
    echo "Invalid product ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product ? htmlspecialchars($product['product_name']) : 'Product Details'; ?> - Product Details</title>
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
                    <li><a class="dropdown-item" href="#">Men</a></li>
                    <li><a class="dropdown-item" href="#">Women</a></li>
                    <li><a class="dropdown-item" href="#">Unisex</a></li>
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


<!-- Product Details Section -->
<div class="container my-5">
    <?php if ($product): ?>
    <div class="row">
        <!-- Thumbnail Gallery and Main Image -->
        <div class="col-md-6 d-flex">
            <div class="thumbnail-gallery me-3">
                <?php
                // Images array
                $images = [];

                if (!empty($product['image'])) {
                    $images[] = 'products/' . htmlspecialchars($product['image']);
                }
                if (!empty($product['extra_image_1'])) {
                    $images[] = 'products/' . htmlspecialchars($product['extra_image_1']);
                }
                if (!empty($product['extra_image_2'])) {
                    $images[] = 'products/' . htmlspecialchars($product['extra_image_2']);
                }

                if (empty($images)) {
                    $images[] = 'images/default-image.jpg';
                }

                // Thumbnails for all images
                foreach ($images as $index => $image_path) {
                    echo '<img src="' . $image_path . '" class="thumbnail-img mb-2" data-index="' . $index . '" 
                        style="width: 70px; height: 70px; object-fit: cover; cursor: pointer; border: 1px solid #ddd; border-radius: 5px;">';
                }
                ?>
            </div>
            <div class="main-image-container" style="margin-left: 10px;">
                <img id="main-image" src="<?php echo $images[0]; ?>" 
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                    class="img-fluid rounded shadow" 
                    style="height: 450px; width: 100%; object-fit: contain;">
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <h1 class="fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 36px; color: #333;"><?php echo htmlspecialchars($product['product_name']); ?></h1>
            <p class="text-muted" style="font-family: 'Roboto', sans-serif; font-size: 16px;"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
            <p class="text-muted" style="font-family: 'Roboto', sans-serif; font-size: 16px;"><strong>Size:</strong> <?php echo htmlspecialchars($product['size']); ?></p>

            <?php if ($product['subcategory'] === 'discount' && isset($product['discounted_price']) && $product['discounted_price'] < $product['price']): ?>
                <h6 class="normal-price" style="font-family: 'Roboto', sans-serif; font-size: 15px; color: #555;"><strong>Normal Price</strong></h6>
                <p class="text-muted" style="font-family: 'Roboto', sans-serif; font-size: 13px;"><del>$<?php echo number_format($product['price'], 2); ?></del></p>
                
                <h6 class="discount-price text-danger" style="font-family: 'Roboto', sans-serif; font-size: 18px; font-weight: bold;">Sale Price</h6>
                <p class="lead fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 20px; color: #e74c3c;">$<?php echo number_format($product['discounted_price'], 2); ?></p>
            <?php else: ?>
                <h6 class="price" style="font-family: 'Roboto', sans-serif; font-size: 18px; color: #555;">Price</h6>
                <p class="lead fw-bold" style="font-family: 'Poppins', sans-serif; font-size: 24px; color: #333;">$<?php echo number_format($product['price'], 2); ?></p>
            <?php endif; ?>
            <p style="font-family: 'Roboto', sans-serif; font-size: 16px;"><strong>Stock:</strong> <?php echo intval($product['stock_quantity']); ?> available</p>

            <!-- Add to Cart Button -->
            <form method="POST" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <button type="submit" class="btn btn-primary w-100" style="font-family: 'Poppins', sans-serif; font-size: 16px; padding: 10px 20px;">Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Product Description-->
    <div class="row mt-4">
        <div class="col-12" style="font-family: 'Roboto', sans-serif; font-size: 16px;">
            <h6 style="font-family: 'Poppins', sans-serif; font-size: 18px; color: #333;"><strong>Description</strong></h6>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
    </div>

    <!-- Display Reviews Section -->
    <div class="row mt-5">
        <div class="col-12" style="font-family: 'Roboto', sans-serif; font-size: 16px;">
            <h6 style="font-family: 'Poppins', sans-serif; font-size: 18px; color: #333;"><strong>Customer Reviews</strong></h6>

            <?php if ($reviews): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold"><?php echo htmlspecialchars($review['user_name']); ?></span>
                            <span class="text-muted"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></span>
                        </div>
                        <div class="d-flex">
                            <span class="text-warning">
                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                    ★
                                <?php endfor; ?>
                                <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                                    ☆
                                <?php endfor; ?>
                            </span>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews yet. Be the first to leave a review!</p>
            <?php endif; ?>

        
        </div>
    </div>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
