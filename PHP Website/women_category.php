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
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
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

    <div>
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="py-3 bg-light">
            <div class="container">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="women_category.php">Category</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Women</li>
                </ol>
            </div>
            <h2 class="fw-bold" style="margin-left: 35px;">Women's Fragrance Collection</h2>
            <p class="text-muted" style="margin-left: 35px;">
                Discover our exclusive Women's Fragrance Collection, where elegance meets femininity. Each scent is crafted to leave a lasting impression, perfect for any occasion.
            </p>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar for Filters -->
                <div class="col-md-3">
                    <div class="border p-5 filter-sidebar sticky-sidebar">
                        <h5 class="mb-3">Filter</h5>

                        <!-- Price Range Filter -->
                        <form method="GET" action="women_category.php">
                            <div class="mb-3">
                                <label for="priceRange" class="form-label">Price Range</label>
                                <div class="d-flex gap-2">
                                    <input type="number" class="form-control" name="min_price" placeholder="Min" min="0" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                                    <input type="number" class="form-control" name="max_price" placeholder="Max" min="0" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                                </div>
                            </div>

                            <!-- Discount Filter -->
                            <div class="mb-3">
                                <label class="form-label">Discount</label>
                                <select class="form-select" name="discount">
                                    <option value="" selected>Any</option>
                                    <option value="5" <?php echo (isset($_GET['discount']) && $_GET['discount'] == '5') ? 'selected' : ''; ?>>5% or more</option>
                                    <option value="10" <?php echo (isset($_GET['discount']) && $_GET['discount'] == '10') ? 'selected' : ''; ?>>10% or more</option>
                                    <option value="20" <?php echo (isset($_GET['discount']) && $_GET['discount'] == '20') ? 'selected' : ''; ?>>20% or more</option>
                                    <option value="30" <?php echo (isset($_GET['discount']) && $_GET['discount'] == '30') ? 'selected' : ''; ?>>30% or more</option>
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select class="form-select" name="category">
                                    <option value="Women" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Women') ? 'selected' : ''; ?>>Women</option>
                                    <option value="Men" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Men') ? 'selected' : ''; ?>>Men</option>
                                    <option value="Unisex" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
                                    <option value="All" <?php echo isset($_GET['category']) && $_GET['category'] == 'All' ? 'selected' : ''; ?>>All Categories</option>
                                </select>
                            </div>

                            <!-- Availability Filter -->
                            <div class="mb-3">
                                <label class="form-label">Availability</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="in_stock" id="inStock" value="1" <?php echo isset($_GET['in_stock']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="inStock">In Stock</label>
                                </div>
                            </div>

                            <!-- Submit Filter Button -->
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>

                <!-- Main Products Section -->
                <div class="col-md-9">
                    <div class="container py-5">
                        <div class="row row-cols-1 row-cols-md-4 g-4">
                            <?php
                            $category = isset($_GET['category']) ? $_GET['category'] : 'Women'; // Default to 'Men' if no category is selected
                            $women_query = "SELECT * FROM products WHERE 1=1";
                            // Apply Category Filter
                            if ($category !== 'All') {
                                $women_query .= " AND category = '$category'";
                            }

                            // Apply Price Filter
                            if (isset($_GET['min_price']) && isset($_GET['max_price']) && is_numeric($_GET['min_price']) && is_numeric($_GET['max_price'])) {
                                $min_price = intval($_GET['min_price']);
                                $max_price = intval($_GET['max_price']);
                                $women_query .= " AND price BETWEEN $min_price AND $max_price";
                            }

                            // Apply Discount Filter
                            if (isset($_GET['discount']) && is_numeric($_GET['discount'])) {
                                $discount = intval($_GET['discount']);
                                $women_query .= " AND discount_percentage >= $discount";
                            }

                            // Apply In-Stock Filter
                            if (isset($_GET['in_stock'])) {
                                $women_query .= " AND stock_quantity > 0";
                            }

                            $women_query .= " ORDER BY created_at";
                            $women_result = mysqli_query($conn, $women_query);

                            // Display Filtered Products
                            while ($women_product = mysqli_fetch_assoc($women_result)) {
                                $stock_quantity = $women_product['stock_quantity'];
                                $is_sold_out = $stock_quantity == 0;

                                $image = isset($women_product['image']) && !empty($women_product['image'])
                                    ? 'products/' . htmlspecialchars($women_product['image'])
                                    : 'images/default-image.jpg';

                                $product_name = htmlspecialchars($women_product['product_name']);
                                $product_price = htmlspecialchars($women_product['price']);
                                $discount_percentage = isset($women_product['discount_percentage']) ? $women_product['discount_percentage'] : 0;

                                // Calculate the discounted price
                                if ($discount_percentage > 0) {
                                    $discounted_price = $product_price - ($product_price * ($discount_percentage / 100));
                                } else {
                                    $discounted_price = $product_price;
                                }
                            ?>
                                <div class="col">
                                    <div class="card h-100 text-center shadow-sm border-0 rounded product-card">
                                        <div class="image-container position-relative overflow-hidden">
                                            <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3"
                                                alt="<?php echo $product_name; ?>"
                                                style="height: 200px; object-fit: contain; transition: transform 0.3s ease;">
                                            <?php if ($is_sold_out): ?>
                                                <span class="sold-out-badge position-absolute top-50 start-50 translate-middle badge rounded-pill bg-danger">Sold Out</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $product_name; ?></h5>
                                            <p class="card-text"><?php echo number_format($discounted_price, 2); ?> $</p>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <!-- About Us Section -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">About Us</h5>
                    <p class="text-muted">Fragrance Haven is your ultimate destination for high-quality perfumes that elevate your senses. Explore our wide range of fragrances designed to suit every occasion and personality.</p>
                </div>

                <!-- Quick Links Section -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="user_index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="women_category.php" class="text-white text-decoration-none">Women’s Collection</a></li>
                        <li><a href="men_category.php" class="text-white text-decoration-none">Men’s Collection</a></li>
                        <li><a href="unisex_category.php" class="text-white text-decoration-none">Unisex Collection</a></li>
                        <li><a href="about_us.php" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="contact_us.php" class="text-white text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Contact Info Section -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Contact Info</h5>
                    <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> 123 Fragrance St, City, Country</p>
                    <p class="text-muted"><i class="fas fa-phone-alt me-2"></i> +123 456 7890</p>
                    <p class="text-muted"><i class="fas fa-envelope me-2"></i> support@fragrancehaven.com</p>
                </div>
            </div>

            <!-- Footer Bottom Section -->
            <div class="row mt-4 border-top pt-3">
                <div class="col-md-6">
                    <p class="text-muted">&copy; 2025 Fragrance Haven. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="https://www.instagram.com/" class="text-white me-3 text-decoration-none"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="https://www.facebook.com/" class="text-white me-3 text-decoration-none"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="https://twitter.com/" class="text-white text-decoration-none"><i class="fab fa-twitter fa-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <!-- End Footer -->

    <!-- Bootstrap JS and Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

</body>

</html>