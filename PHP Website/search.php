<?php
// Start session
if (!isset($_SESSION)) {
    session_start();
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

// Initialize search query and filters
$query = isset($_GET['query']) ? $_GET['query'] : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$discount = isset($_GET['discount']) ? intval($_GET['discount']) : 0;
$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$in_stock = isset($_GET['in_stock']) ? 1 : 0;

// Base SQL query with the search condition
$sql = "SELECT * FROM products WHERE (product_name LIKE ? OR description LIKE ?)";
$params = [];
$types = "ss";

// Add the search term to the query
$params[] = '%' . $query . '%';
$params[] = '%' . $query . '%';

// Add additional filters
if ($min_price > 0) {
    $sql .= " AND price >= ?";
    $params[] = $min_price;
    $types .= "d"; // Float
}

if ($max_price > 0) {
    $sql .= " AND price <= ?";
    $params[] = $max_price;
    $types .= "d"; // Float
}

if ($discount > 0) {
    $sql .= " AND discount_percentage >= ?";
    $params[] = $discount;
    $types .= "i"; // Integer
}

if ($category != 'All') {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s"; // String
}

if ($in_stock) {
    $sql .= " AND stock_quantity > 0";
}

// Add sorting
$sql .= " ORDER BY created_at DESC";

// Prepare and execute the query
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$search_result = mysqli_stmt_get_result($stmt);

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragrance Haven</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>              
                <li class="breadcrumb-item active" aria-current="page">Search Results</li>
            </ol>
        </div>
        </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar for Filters -->
            <div class="col-md-3">
                <div class="border p-5 filter-sidebar sticky-sidebar">
                    <h5 class="mb-3">Filter</h5>
                    <form method="GET" action="search.php">
    <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">
    
    <!-- Price Range Filter -->
    <div class="mb-3">
        <label for="priceRange" class="form-label">Price Range</label>
        <div class="d-flex gap-2">
            <input type="number" class="form-control" name="min_price" placeholder="Min" min="0" value="<?php echo htmlspecialchars($min_price); ?>">
            <input type="number" class="form-control" name="max_price" placeholder="Max" min="0" value="<?php echo htmlspecialchars($max_price); ?>">
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
            <option value="All" <?php echo (isset($_GET['category']) && $_GET['category'] == 'All') ? 'selected' : ''; ?>>All Categories</option>
            <option value="Men" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Men') ? 'selected' : ''; ?>>Men</option>
            <option value="Women" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Women') ? 'selected' : ''; ?>>Women</option>
            <option value="Unisex" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Unisex') ? 'selected' : ''; ?>>Unisex</option>
        </select>
    </div>

    <!-- Availability Filter -->
    <div class="mb-3">
        <label class="form-label">Availability</label>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="in_stock" id="inStock" value="1" <?php echo (isset($_GET['in_stock']) && $_GET['in_stock'] == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="inStock">In Stock</label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
</form>

                </div>
            </div>

            <div class="col-md-9">
                <?php if (mysqli_num_rows($search_result) > 0) : ?>
                    <?php if ($query) : ?>
                        <h3>Search Results for "<?php echo htmlspecialchars($query); ?>"</h3>
                    <?php else : ?>
                        <h3>Filtered Products</h3>
                    <?php endif; ?>

                    <div class="row row-cols-1 row-cols-md-4 g-4">
                        <?php while ($product = mysqli_fetch_assoc($search_result)) : ?>
                            <div class="col">
                                <div class="card h-100 text-center shadow-sm border-0 rounded product-card">
                                    <div class="image-container position-relative overflow-hidden">
                                        <img src="products/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top img-fluid p-3"
                                            alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                            style="height: 200px; object-fit: contain;">
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-truncate"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                        <p class="card-text text-muted">
                                            <?php if ($product['discount_percentage'] > 0) : ?>
                                                <span class="text-decoration-line-through">$<?php echo number_format($product['price'], 2); ?></span>
                                                <span class="text-danger ms-2">Now $<?php echo number_format($product['discounted_price'], 2); ?></span>
                                            <?php else : ?>
                                                $<?php echo number_format($product['price'], 2); ?>
                                            <?php endif; ?>
                                        </p>
                                        <?php if ($product['stock_quantity'] == 0) : ?>
                                            <p class="text-danger fw-bold">Sold Out</p>
                                        <?php else : ?>
                                            <a href="add_to_cart.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-outline-primary btn-sm">Add to Cart</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <p>No results found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>