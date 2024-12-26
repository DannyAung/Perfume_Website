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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from the database
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(":email", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Debug: Check fetched user data
        error_log("Fetched user data: " . print_r($user, true));
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_name'] = $user['user_name']; // Correct variable

            // Debug: Check session variables
            error_log("Session username: " . $_SESSION['user_name']);

            header("Location: viewProduct.php");
            exit;
        } else {
            $error_message = "Invalid email or password.";
        }
    }
}


// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
// Fetch products
$sql = "SELECT product_name, price, image FROM products"; // Ensure table columns are correct
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error in query: " . mysqli_error($conn));
}

// Fetch discounted products
$discounted_query = "SELECT * FROM products WHERE subcategory = 'discount' ORDER BY created_at DESC LIMIT 4";
$discounted_result = mysqli_query($conn, $discounted_query);
if (!$discounted_result) {
    die("Error fetching discounted products: " . mysqli_error($conn));
}

// Handle product insertion (if POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'] ?? '';
    $size = $_POST['size'] ?? null; // Optional size
    $discounted_price = $_POST['discounted_price'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $upload_dir = 'products/';
    $upload_path = $upload_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        // Insert product
        $insert_query = "INSERT INTO products 
                         (product_name, image, description, price, stock_quantity, category, subcategory, size, discounted_price) 
                         VALUES ('$product_name', '$upload_path', '$description', $price, $stock_quantity, '$category', '$subcategory', '$size', $discounted_price)";
        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['success'] = 'Product added successfully!';
            header('Location: manage_products.php');
            exit;
        } else {
            echo 'Error adding product: ' . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = 'Error uploading image.';
    }
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

  <!-- Carousel -->
<div id="posterCarousel" class="carousel slide my-2" data-bs-ride="carousel">
    <div class="carousel-inner">
        
        <div class="carousel-item active position-relative">
        <img src="images/poster333.png" class="d-block w-100" alt="Poster 3"> 
        <a href="add_to_cart.php" class="btn btn-primary order-now-btn position-absolute">Order Now</a>
    </div>
    
        <div class="carousel-item">
            <img src="images/poster111.png" class="d-block w-100" alt="Poster 1">
        </div>

        <div class="carousel-item">
            <img src="images/poster222.png" class="d-block w-100" alt="Poster 2">
        </div>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#posterCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#posterCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Product Grid -->
<div class="container my-5">
<h1 class="text-center mb-4"><b>Our Products</b></h1>

<h2 class="mb-4 mt-5">Discounted Products</h2>
<div class="row row-cols-1 row-cols-md-4 g-4">
    <?php 
    if ($discounted_result) {
        while ($discounted_product = mysqli_fetch_assoc($discounted_result)) {
            // Get stock quantity and check if it's sold out
            $stock_quantity = $discounted_product['stock_quantity'];
            $is_sold_out = $stock_quantity == 0;

            // Image path logic
            $image = isset($discounted_product['image']) && !empty($discounted_product['image']) 
                ? 'products/' . htmlspecialchars($discounted_product['image']) 
                : 'images/default-image.jpg';

            // Get product details
            $product_name = htmlspecialchars($discounted_product['product_name']);
            $product_price = htmlspecialchars($discounted_product['price']);
            $product_discounted_price = htmlspecialchars($discounted_product['discounted_price']);
    ?>
        <div class="col">
            <div class="card h-100 text-center shadow-sm border-0 rounded">
                <!-- Image container with dynamic height and hover effect -->
                <div class="image-container" style="overflow: hidden; position: relative; margin: 0 auto;">
                    <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3" 
                    alt="<?php echo $product_name; ?>" 
                    style="height: 150px; object-fit: contain; transition: transform 0.3s;">
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <!-- Product Name -->
                    <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>

                    <!-- Pricing Section -->
                    <div class="pricing mb-3">
                        <h6 class="normal-price text-muted">
                            <h7>Normal Price</h7><br>
                            <del>$<?php echo number_format($product_price, 2); ?></del>
                        </h6>

                        <h7>Sale Price</h7><br>
                        <h6 class="discount-price text-danger fw-bold">
                            $<?php echo number_format($product_discounted_price, 2); ?>
                        </h6>
                    </div>

                    <!-- Sold Out Message and Prevent Add to Cart -->
                    <?php if ($is_sold_out): ?>
                        <p class="text-danger fw-bold">Sold Out</p>
                        <button class="btn btn-outline-secondary btn-sm" disabled>Out of Stock</button>
                    <?php else: ?>
                        <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                            <input type="hidden" name="product_id" value="<?php echo $discounted_product['product_id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($discounted_product['product_name']); ?>">
                            <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($discounted_product['discounted_price']); ?>">
                            <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($discounted_product['image']); ?>">

                            <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm flex-grow-1">
                                Add to Cart
                            </button>
                            <a href="product_details.php?product_id=<?php echo $discounted_product['product_id']; ?>" class="btn btn-primary btn-sm flex-grow-1">
                                View Details
                            </a>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    <?php 
        }
    } else {
        echo '<p class="text-center text-muted">No discounted products found.</p>';
    }
    ?>
</div>



<h2 class="text-center mb-4 mt-5"></h2>
<div class=" mb-4">
    <button id="popularBtn" class="btn btn-dark" onclick="showPopular()">Best Sellers</button>
    <button id="latestBtn" class="btn btn-outline-dark" onclick="showLatest()">New Arrivals</button>
</div>

<div id="popularProducts" class="row row-cols-1 row-cols-md-4 g-4">
    <?php 
    $popular_query = "SELECT * FROM products WHERE subcategory = 'popular' ORDER BY created_at DESC LIMIT 4";
    $popular_result = mysqli_query($conn, $popular_query);
        
    while ($popular_product = mysqli_fetch_assoc($popular_result)) {
        $stock_quantity = $popular_product['stock_quantity'];
        $is_sold_out = $stock_quantity == 0;

        $image = isset($popular_product['image']) && !empty($popular_product['image']) 
            ? 'products/' . htmlspecialchars($popular_product['image']) 
            : 'images/default-image.jpg';

        $product_name = htmlspecialchars($popular_product['product_name']);
        $product_price = htmlspecialchars($popular_product['price']);
    ?>
        <div class="col">
            <div class="card h-100 text-center shadow d-flex flex-column">
                <div class="image-container" style="width: auto; margin: 0 auto;">
                    <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($popular_product['product_name']); ?>" 
                    style="max-height: 150px; width: 100%; object-fit: contain;">
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo $product_name; ?></h5>
                    <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
                    <?php if ($is_sold_out): ?>
                        <p class="text-danger fw-bold">Sold Out</p>
                        <button class="btn btn-outline-secondary btn-sm" disabled>Out of Stock</button>
                    <?php else: ?>
                        <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                            <input type="hidden" name="product_id" value="<?php echo $popular_product['product_id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm flex-grow-1">
                                Add to Cart
                            </button>
                            <a href="product_details.php?product_id=<?php echo $popular_product['product_id']; ?>" class="btn btn-primary btn-sm flex-grow-1">
                                View Details
                            </a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<div id="latestProducts" class="row row-cols-1 row-cols-md-4 g-4" style="display: none;">
    <?php 
    $latest_query = "SELECT * FROM products WHERE subcategory = 'latest' ORDER BY created_at DESC LIMIT 4";
    $latest_result = mysqli_query($conn, $latest_query);
        
    while ($latest_product = mysqli_fetch_assoc($latest_result)) {
        $stock_quantity = $latest_product['stock_quantity'];
        $is_sold_out = $stock_quantity == 0;

        $image = isset($latest_product['image']) && !empty($latest_product['image']) 
            ? 'products/' . htmlspecialchars($latest_product['image']) 
            : 'images/default-image.jpg';

        $product_name = htmlspecialchars($latest_product['product_name']);
        $product_price = htmlspecialchars($latest_product['price']);
    ?>
        <div class="col">
            <div class="card h-100 text-center shadow d-flex flex-column">
                <div class="image-container" style="width: auto; margin: 0 auto;">
                    <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($latest_product['product_name']); ?>" 
                    style="max-height: 150px; width: 100%; object-fit: contain;">
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo $product_name; ?></h5>
                    <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
                    <?php if ($is_sold_out): ?>
                        <p class="text-danger fw-bold">Sold Out</p>
                        <button class="btn btn-outline-secondary btn-sm" disabled>Out of Stock</button>
                    <?php else: ?>
                        <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                            <input type="hidden" name="product_id" value="<?php echo $latest_product['product_id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm flex-grow-1">
                                Add to Cart
                            </button>
                            <a href="product_details.php?product_id=<?php echo $latest_product['product_id']; ?>" class="btn btn-primary btn-sm flex-grow-1">
                                View Details
                            </a>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    function showPopular() {
        document.getElementById("popularProducts").style.display = "flex";
        document.getElementById("latestProducts").style.display = "none";
        document.getElementById("popularBtn").classList.add("btn-dark");
        document.getElementById("popularBtn").classList.remove("btn-outline-dark");
        document.getElementById("latestBtn").classList.remove("btn-dark");
        document.getElementById("latestBtn").classList.add("btn-outline-dark");
    }

    function showLatest() {
        document.getElementById("popularProducts").style.display = "none";
        document.getElementById("latestProducts").style.display = "flex";
        document.getElementById("latestBtn").classList.add("btn-dark");
        document.getElementById("latestBtn").classList.remove("btn-outline-dark");
        document.getElementById("popularBtn").classList.remove("btn-dark");
        document.getElementById("popularBtn").classList.add("btn-outline-dark");
    }
</script>


<h2 class="mb-4 mt-5">Featured Products</h2>
<div class="row row-cols-1 row-cols-md-4 g-4">
    <?php 
    // Query to fetch featured products based on subcategory
    $featured_query = "SELECT * FROM products WHERE subcategory = 'featured' ORDER BY created_at DESC LIMIT 4";
    $featured_result = mysqli_query($conn, $featured_query);
        
    while ($featured_product = mysqli_fetch_assoc($featured_result)) {
        // Get stock quantity and check if it's sold out
        $stock_quantity = $featured_product['stock_quantity'];
        $is_sold_out = $stock_quantity == 0;

        // Image path logic
        $image = isset($featured_product['image']) && !empty($featured_product['image']) 
            ? 'products/' . htmlspecialchars($featured_product['image']) 
            : 'images/default-image.jpg';

        // Check if image exists, otherwise fallback
        if (!file_exists($image)) {
            $image = 'images/default-image.jpg';  // Fallback image
        }

        $product_name = htmlspecialchars($featured_product['product_name']);
        $product_price = htmlspecialchars($featured_product['price']);
    ?>
        <div class="col">
            <div class="card h-100 text-center shadow d-flex flex-column">
                <!-- Image container with dynamic height -->
                <div class="image-container" style="width: auto; margin: 0 auto;">
                    <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($featured_product['product_name']); ?>" 
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
                            <input type="hidden" name="product_id" value="<?php echo $featured_product['product_id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($featured_product['product_name']); ?>">
                            <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($featured_product['price']); ?>">
                            <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($featured_product['image']); ?>">

                            <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm flex-grow-1">
                                Add to Cart
                            </button>
                            <a href="product_details.php?product_id=<?php echo $featured_product['product_id']; ?>" class="btn btn-primary btn-sm flex-grow-1">
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
