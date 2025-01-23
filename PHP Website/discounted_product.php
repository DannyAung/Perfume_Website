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
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <a class="navbar-brand d-flex align-items-center" href="user_index.php">
                <img src="./images/perfume_logo.png" alt="Logo" style="width:50px; height:auto;">
                <b class="ms-2" style="font-family: 'Roboto', sans-serif; font-weight: 300; color: #333;">FRAGRANCE HAVEN</b>
            </a>

            <!-- Toggler for Small Screens -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex flex-column flex-lg-row w-100 align-items-center">

                    <!-- Modern Search Bar in the Center -->
                    <div class="search-bar-container mx-lg-auto my- my-lg-0 w-100 w-lg-auto">
                        <form method="GET" action="search.php" class="search-form d-flex">
                            <input type="text" class="form-control border-end-0 search-input" name="query" placeholder="Search for a product..." aria-label="Search" required>
                            <button class="btn btn-primary search-btn border-start-1 rounded-end-2 px-4  shadow-lg" type="submit">
                                <i class="bi bi-search"></i> <!-- FontAwesome or Bootstrap Icons -->
                            </button>
                        </form>
                    </div>

                    <!-- Display Username or Guest -->
                    <span class="navbar-text mx-lg-3 my-2 my-lg-0 text-center">
                        Welcome, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>!
                    </span>

                    <!-- Account Dropdown for Logged-In Users -->
                    <?php if ($is_logged_in): ?>
                        <div class="dropdown mx-lg-3 my-2 my-lg-0">
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


                    <!-- Login and Cart Buttons -->
                    <div class="d-flex justify-content-center justify-content-lg-end my-2 my-lg-0">
                        <?php if (!$is_logged_in): ?>
                            <a href="user_login.php" class="btn login-btn me-3 ">Login/Register</a>
                        <?php endif; ?>
                        <!-- Favorite Link -->
                        <a class="nav-link d-flex align-items-center justify-content-center mx-lg-3 my-2 my-lg-0" href="favorite.php">
                            <i class="bi bi-heart fs-5"></i> <!-- Larger Icon -->
                        </a>
                        <a href="add_to_cart.php" class="btn cart-btn" id="cart-button">
                            <img src="./images/cart-icon.jpg" alt="Cart" style="width:24px; height:24px; margin-right:2px;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div>
        <!-- Breadcrumb Navigation -->
        <nav aria-label="breadcrumb" class="py-3 bg-light">
            <div class="container">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Discount items</li>
                </ol>
            </div>
            <h2 class="fw-bold" style="margin-left: 35px;">Discounted Fragrance Collection</h2>
            <p class="text-muted" style="margin-left: 35px;">
                Elevate your style with our exclusive Discounted Fragrance Collection, blending timeless sophistication with modern charm.
                Each scent is carefully crafted to leave a lasting impression, perfect for every occasion.
            </p>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar for Filters -->
                <div class="col-md-3">
                    <div class="border p-5 filter-sidebar sticky-sidebar">
                        <h5 class="mb-3">Filter</h5>

                        <!-- Price Range Filter -->
                        <form method="GET" action="discounted_product.php">
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
                                <option value="All" <?php echo isset($_GET['category']) && $_GET['category'] == 'All' ? 'selected' : ''; ?>>All</option>
                                    <option value="Men" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Men') ? 'selected' : ''; ?>>Men</option>
                                    <option value="Women" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Women') ? 'selected' : ''; ?>>Women</option>
                                    <option value="Unisex" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
                                   
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

                            $category = isset($_GET['category']) ? $_GET['category'] : 'All'; // Default to 'All' if no category is selected

                            // Start the base query
                            $discount_query = "SELECT * FROM products WHERE 1=1";

                            // Apply the category filter if a specific category is selected
                            if ($category !== 'All') {
                                // Filter products by the selected category
                                $discount_query .= " AND category = '$category'";
                            }

                            // Ensure only discounted products are shown (using the subcategory column for discounts)
                            $discount_query .= " AND subcategory != ''"; // Ensure the subcategory is not empty
                            $discount_query .= " AND discount_percentage > 0"; // Only include discounted products

                            // Execute the query
                            $result = mysqli_query($conn, $discount_query);

                            // Apply Price Filter
                            if (isset($_GET['min_price']) && isset($_GET['max_price']) && is_numeric($_GET['min_price']) && is_numeric($_GET['max_price'])) {
                                $min_price = intval($_GET['min_price']);
                                $max_price = intval($_GET['max_price']);
                                $discount_query .= " AND price BETWEEN $min_price AND $max_price";
                            }

                            // Apply Discount Filter
                            if (isset($_GET['discount']) && is_numeric($_GET['discount'])) {
                                $discount = intval($_GET['discount']);
                                $discount_query .= " AND discount_percentage >= $discount";
                            }

                            // Apply In-Stock Filter
                            if (isset($_GET['in_stock'])) {
                                $discount_query .= " AND stock_quantity > 0";
                            }


                            $discount_query .= " ORDER BY created_at";
                            $discount_result = mysqli_query($conn, $discount_query);

                            // Display Filtered Products
                            while ($discount_product = mysqli_fetch_assoc($discount_result)) {
                                $stock_quantity = $discount_product['stock_quantity'];
                                $is_sold_out = $stock_quantity == 0;

                                $image = isset($discount_product['image']) && !empty($discount_product['image'])
                                    ? 'products/' . htmlspecialchars($discount_product['image'])
                                    : 'images/default-image.jpg';

                                $product_name = htmlspecialchars($discount_product['product_name']);
                                $product_price = htmlspecialchars($discount_product['price']);
                                $discount_percentage = isset($discount_product['discount_percentage']) ? $discount_product['discount_percentage'] : 0;

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
                                            <!-- Discount Badge -->
                                            <?php if ($discount_percentage > 0): ?>
                                                <div class="discount-badge position-absolute top-0 start-0 bg-danger text-white px-2 py-1 rounded-end"
                                                    style="font-size: 0.9rem; z-index: 10;"> <!-- Added z-index here -->
                                                    <?php echo $discount_percentage; ?>% OFF
                                                </div>
                                            <?php endif; ?>

                                            <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3"
                                                alt="<?php echo $product_name; ?>"
                                                style="height: 200px; object-fit: contain; transition: transform 0.3s ease-in-out;">
                                            <!-- Sold Out Badge -->
                                            <?php if ($is_sold_out): ?>
                                                <div class="position-absolute top-50 start-50 translate-middle w-100 h-100 d-flex justify-content-center align-items-center"
                                                    style="background: rgba(52, 51, 51, 0.7);">
                                                    <div class="sold-out-badge text-center bg-red px-2 py-0 rounded-pill shadow-sm"
                                                        style="color:rgb(253, 253, 255); font-weight: 550; border: 2px">
                                                        Sold Out
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Hover Overlay -->
                                            <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                                                style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease-in-out;">
                                                <?php if (!$is_sold_out): ?>
                                                    <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                                        <input type="hidden" name="product_id" value="<?php echo $discount_product['product_id']; ?>">
                                                        <button type="submit" name="add_to_cart" class="btn btn-outline-light btn-sm">
                                                            <i class="fa fa-cart-plus"></i>
                                                        </button>
                                                        <a href="product_details.php?product_id=<?php echo $discount_product['product_id']; ?>" class="btn btn-light btn-sm">
                                                            <i class="fa fa-info-circle"></i>
                                                        </a>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>
                                            <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const productCards = document.querySelectorAll('.product-card .image-container');
            productCards.forEach(card => {
                card.addEventListener('mouseover', () => {
                    card.querySelector('.hover-overlay').style.opacity = '1';
                    card.querySelector('img').style.transform = 'scale(1.1)';
                });

                card.addEventListener('mouseout', () => {
                    card.querySelector('.hover-overlay').style.opacity = '0';
                    card.querySelector('img').style.transform = 'scale(1)';
                });
            });
        </script>
        <?php include 'footer.php'; ?>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <!-- Latest Font Awesome version -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

        <!-- Include Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

        <!-- Include Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Include Font Awesome for Icons -->
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>

</html>
<?php
// Close the database connection
mysqli_close($conn);
?>