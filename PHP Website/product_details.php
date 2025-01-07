<?php
session_start();  // Start the session to access session variables

// Check if u

require_once 'db_connection.php';  // Ensure the connection file is correct


$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

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
    <title>Fragrance Haven</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <a class="navbar-brand d-flex align-items-center" href="user_index.php">
                <img src="./images/perfume_logo.png" alt="Logo" style="width:50px; height:auto;">
                <b class="ms-2 dm-serif-display-regular-italic custom-font-color">FRAGRANCE HAVEN</b>
            </a>

            <!-- Collapsible Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex flex-column flex-lg-row w-100 align-items-center">

                    <!-- Modern Search Bar in the Center -->
                    <div class="search-bar-container mx-auto my-2 my-lg-0">
                        <form method="GET" action="search.php" class="search-form mb-2">
                            <div class="input-group">
                                <input type="text" class="form-control border-end-0 search-input" name="query" placeholder="Search for a product..." aria-label="Search" required>
                                <button class="btn btn-primary search-btn border-start-0 rounded-end px-4 py-2 shadow-lg" type="submit">
                                    <i class="bi bi-search"></i> <!-- FontAwesome or Bootstrap Icons -->
                                </button>
                            </div>
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


    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item" onclick="history.back()">Back</li>
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Details</li>
            </ol>
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
                        <!-- Add to Cart Form -->
                        <form method="POST" action="add_to_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="number" name="quantity" value="1" min="1" class="form-control mb-3" style="width: 100px;">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
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
                                        <span class="text-muted"><?php echo date('F d, Y', strtotime($review['created_at'])); ?></span>
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

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const thumbnails = document.querySelectorAll('.thumbnail-img');
                const mainImage = document.getElementById('main-image');

                thumbnails.forEach((thumbnail, index) => {
                    thumbnail.addEventListener('click', () => {
                        // Update the main image's source
                        mainImage.src = thumbnail.src;

                        // Add an active class to the clicked thumbnail
                        thumbnails.forEach(thumb => thumb.style.border = '1px solid #ddd');
                        thumbnail.style.border = '2px solid #007bff';
                    });
                });
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>