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
                    <li class="breadcrumb-item"><a href="men_category.php">Category</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Latest items</li>
                </ol>
            </div>
            <h2 class="fw-bold" style="margin-left: 35px;">Latest Fragrance Collection</h2>
            <p class="text-muted" style="margin-left: 35px;">
                Elevate your style with our exclusive Latest Fragrance Collection, blending timeless sophistication with modern charm.
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
                        <form method="GET" action="latest_product.php">
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

                                    <option value="Men" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Men') ? 'selected' : ''; ?>>Men</option>
                                    <option value="Women" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Women') ? 'selected' : ''; ?>>Women</option>
                                    <option value="Unisex" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
                                    <option value="All" <?php echo isset($_GET['category']) && $_GET['category'] == 'All' ? 'selected' : ''; ?>>All</option>

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
                <!-- Main latest Products Section -->
                <div class="col-md-9">
                    <div class="container py-5">
                        <div class="row row-cols-1 row-cols-md-4 g-4">
                            <?php
                            // Set default category to 'All' if not selected
                            $category = isset($_GET['category']) ? $_GET['category'] : 'All';

                            $latest_query = "SELECT * FROM products WHERE subcategory = 'latest'"; // Fetch products where subcategory is 'latest'

                            // Apply the category filter if a specific category is selected
                            if ($category !== 'All') {
                                $latest_query .= " AND category = '$category'";
                            }

                            // Execute the query
                            $result = mysqli_query($conn, $latest_query);

                            // Apply Price Filter
                            if (isset($_GET['min_price']) && isset($_GET['max_price']) && is_numeric($_GET['min_price']) && is_numeric($_GET['max_price'])) {
                                $min_price = intval($_GET['min_price']);
                                $max_price = intval($_GET['max_price']);
                                $latest_query .= " AND price BETWEEN $min_price AND $max_price";
                                $result = mysqli_query($conn, $latest_query);
                            }

                            // Apply In-Stock Filter
                            if (isset($_GET['in_stock'])) {
                                $latest_query .= " AND stock_quantity > 0";
                                $result = mysqli_query($conn, $latest_query);
                            }

                            // Display latest Products
                            while ($product = mysqli_fetch_assoc($result)) {
                                $stock_quantity = $product['stock_quantity'];
                                $is_sold_out = $stock_quantity == 0;

                                $image = isset($product['image']) && !empty($product['image'])
                                    ? 'products/' . htmlspecialchars($product['image'])
                                    : 'images/default-image.jpg';

                                $product_name = htmlspecialchars($product['product_name']);
                                $product_price = htmlspecialchars($product['price']);
                                $discount_percentage = isset($product['discount_percentage']) ? $product['discount_percentage'] : 0;

                                // Calculate the discounted price if discount exists
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
                                                <div class="discount-badge position-absolute top-0 start-0 bg-danger text-white px-2 py-1 rounded-end" style="font-size: 0.9rem;">
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
                                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                        <button type="submit" name="add_to_cart" class="btn btn-outline-light btn-sm">
                                                            <i class="fa fa-cart-plus"></i>
                                                        </button>
                                                        <a href="product_details.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-light btn-sm">
                                                            <i class="fa fa-info-circle"></i>
                                                        </a>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>
                                            <p class="card-text text-muted">$<?php echo number_format($discounted_price, 2); ?></p>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">About Us</h5>
                    <p class="text-muted">Fragrance Haven is your ultimate destination for high-quality perfumes that elevate your senses. Explore our wide range of fragrances designed to suit every occasion and personality.</p>
                </div>

                <div class="col-md-2 mb-4">
                    <h5 class="mb-1">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="user_index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="women_category.php" class="text-white text-decoration-none">Women’s Collection</a></li>
                        <li><a href="men_category.php" class="text-white text-decoration-none">Men’s Collection</a></li>
                        <li><a href="unisex_category.php" class="text-white text-decoration-none">Unisex Collection</a></li>
                        <li><a href="about_us.php" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="contact_us.php" class="text-white text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>

                <div class="col-md-2 mb-4">
                    <h5 class="mb-1">Customer Care</h5>
                    <ul class="list-unstyled">
                        <li><a href="privacy_policy.php" class="text-white text-decoration-none">Privacy Policy</a></li>
                        <li><a href="term_and_conditions.php" class="text-white text-decoration-none">Terms and Conditions</a></li>
                        <li><a href="faq.php" class="text-white text-decoration-none">FAQ</a></li>
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <h5 class="mb-4">Contact Info</h5>
                    <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> Pyi Yeik Thar Street, Kamayut, Yangon, Myanmar</p>
                    <p class="text-muted"><i class="fas fa-phone-alt me-2"></i> +959450197415</p>
                    <p class="text-muted"><i class="fas fa-envelope me-2"></i> support@fragrancehaven.com</p>
                </div>
            </div>

            <div class="row mt-4 border-top pt-3">
                <div class="col-md-6">
                    <p class="text-muted">&copy; 2025 Fragrance Haven. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="https://www.instagram.com/" class="text-white me-3 text-decoration-none" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="https://www.facebook.com/" class="text-white me-3 text-decoration-none" target="_blank"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="https://twitter.com/" class="text-white text-decoration-none" target="_blank"><i class="fab fa-twitter fa-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>
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

        <footer class="bg-dark text-white py-5">
            <div class="container">
                <div class="row">

                    <div class="col-md-4 mb-4">
                        <h5 class="mb-3">About Us</h5>
                        <p class="text-muted">Fragrance Haven is your ultimate destination for high-quality perfumes that elevate your senses. Explore our wide range of fragrances designed to suit every occasion and personality.</p>
                    </div>

                    <div class="col-md-2 mb-4">
                        <h5 class="mb-1">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="user_index.php" class="text-white text-decoration-none">Home</a></li>
                            <li><a href="women_category.php" class="text-white text-decoration-none">Women’s Collection</a></li>
                            <li><a href="men_category.php" class="text-white text-decoration-none">Men’s Collection</a></li>
                            <li><a href="unisex_category.php" class="text-white text-decoration-none">Unisex Collection</a></li>
                            <li><a href="about_us.php" class="text-white text-decoration-none">About Us</a></li>
                            <li><a href="contact_us.php" class="text-white text-decoration-none">Contact Us</a></li>
                        </ul>
                    </div>

                    <div class="col-md-2 mb-4">
                        <h5 class="mb-1">Customer Care</h5>
                        <ul class="list-unstyled">
                            <li><a href="privacy_policy.php" class="text-white text-decoration-none">Privacy Policy</a></li>
                            <li><a href="term_and_conditions.php" class="text-white text-decoration-none">Terms and Conditions</a></li>
                        </ul>
                    </div>

                    <div class="col-md-3 mb-4">
                        <h5 class="mb-4">Contact Info</h5>
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> Pyi Yeik Thar Street, Kamayut, Yangon, Myanmar</p>
                        <p class="text-muted"><i class="fas fa-phone-alt me-2"></i> +959450197415</p>
                        <p class="text-muted"><i class="fas fa-envelope me-2"></i> support@fragrancehaven.com</p>
                    </div>
                </div>

                <div class="row mt-4 border-top pt-3">
                    <div class="col-md-6">
                        <p class="text-muted">&copy; 2025 Fragrance Haven. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="https://www.instagram.com/" class="text-white me-3 text-decoration-none" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="https://www.facebook.com/" class="text-white me-3 text-decoration-none" target="_blank"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="https://twitter.com/" class="text-white text-decoration-none" target="_blank"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </footer>


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