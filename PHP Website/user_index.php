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
$discounted_query = "SELECT * FROM products WHERE subcategory = 'discount' ORDER BY created_at";
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


            <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <!-- Collapsible Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex flex-column flex-lg-row w-100 align-items-center">
                    <!-- Search Bar in the Center -->
                    <div class="mx-auto my-2 my-lg-0">
                        <form method="GET" action="search.php" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="query" placeholder="Search for a product..." aria-label="Search" required>
                                <button class="btn btn-primary" type="submit">Search</button>
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
        <h1 class="text-center mb-4" style="font-family: 'Playfair Display', serif;"><b>Our Products</b></h1>
        <h2 class="mb-4 mt-5" style="font-family: 'Roboto', sans-serif; font-weight: 500;">Discounted Products</h2>
        <div class="row row-cols-1 row-cols-md-4 g-4">
            <?php
            if ($discounted_result) {
                while ($discounted_product = mysqli_fetch_assoc($discounted_result)) {
                    $stock_quantity = $discounted_product['stock_quantity'];
                    $is_sold_out = $stock_quantity == 0;

                    $image = isset($discounted_product['image']) && !empty($discounted_product['image'])
                        ? 'products/' . htmlspecialchars($discounted_product['image'])
                        : 'images/default-image.jpg';

                    $product_name = htmlspecialchars($discounted_product['product_name']);
                    $product_price = htmlspecialchars($discounted_product['price']);
                    $product_discounted_price = htmlspecialchars($discounted_product['discounted_price']);
            ?>
                    <div class="col">
                        <div class="card h-100 text-center shadow-sm border-0 rounded product-card">
                            <div class="image-container position-relative overflow-hidden">
                                <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3"
                                    alt="<?php echo $product_name; ?>"
                                    style="height: 200px; object-fit: contain; transition: transform 0.3s ease-in-out;">
                                <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease-in-out;">
                                    <?php if (!$is_sold_out): ?>
                                        <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                            <input type="hidden" name="product_id" value="<?php echo $discounted_product['product_id']; ?>">
                                            <input type="hidden" name="product_name" value="<?php echo $product_name; ?>">
                                            <input type="hidden" name="product_price" value="<?php echo $product_discounted_price; ?>">
                                            <input type="hidden" name="product_image" value="<?php echo $image; ?>">

                                            <button type="submit" name="add_to_cart" class="btn btn-outline-light btn-sm">
                                                <i class="fa fa-cart-plus"></i>
                                            </button>
                                            <a href="product_details.php?product_id=<?php echo $discounted_product['product_id']; ?>" class="btn btn-light btn-sm">
                                                <i class="fa fa-info-circle"></i>
                                            </a>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>
                                <div class="pricing mb-3">
                                    <h6 class="normal-price text-muted">
                                        <del>$<?php echo number_format($product_price, 2); ?></del>
                                    </h6>
                                    <h6 class="discount-price text-danger fw-bold">
                                        $<?php echo number_format($product_discounted_price, 2); ?>
                                    </h6>
                                </div>
                                <?php if ($is_sold_out): ?>
                                    <p class="text-danger fw-bold">Sold Out</p>
                                    <button class="btn btn-outline-secondary btn-sm" disabled>Out of Stock</button>
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


        <!-- Category Section -->
        <div class="container my-5">
            <h2 class="text-center mb-4 mt-5" style="font-family: 'Roboto', sans-serif; font-weight: 500;">Browse by Category</h2>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Men Category -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded">
                        <img src="images/men-category.jpg" class="card-img-top" alt="Men Products" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title" style="font-family: 'Roboto', sans-serif; font-weight: 600;">Men</h5>
                            <p class="card-text">Explore our latest collection of men's fashion products.</p>
                            <!-- Custom Blue button -->
                            <a href="men_category.php" class="btn custom-blue-btn">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Women Category -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded">
                        <img src="images/women-category.jpg" class="card-img-top" alt="Women Products" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title" style="font-family: 'Roboto', sans-serif; font-weight: 600;">Women</h5>
                            <p class="card-text">Discover stylish and elegant products for women.</p>
                            <!-- Custom Blue button -->
                            <a href="women_category.php" class="btn custom-blue-btn">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Unisex Category -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded">
                        <img src="images/unisex-category.jpg" class="card-img-top" alt="Unisex Products" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title" style="font-family: 'Roboto', sans-serif; font-weight: 600;">Unisex</h5>
                            <p class="card-text">Browse unisex products suitable for everyone.</p>
                            <!-- Custom Blue button -->
                            <a href="unisex_category.php" class="btn custom-blue-btn">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <h2 class="text-center mb-4 mt-5"></h2>
        <div class="d-flex justify-content-center mb-4 gap-3">
            <button id="popularBtn" class="btn btn-dark px-4 py-2 rounded-pill" onclick="showPopular()">
                <i class="fa fa-star me-2"></i> Popular
            </button>
            <button id="latestBtn" class="btn btn-outline-dark px-4 py-2 rounded-pill" onclick="showLatest()">
                <i class="fa fa-clock me-2"></i> New Arrival
            </button>
        </div>


       <!-- Carousel for Popular Products -->
<div id="popularProductsCarousel" class="carousel slide" data-bs-ride="false">
    <div class="carousel-inner">
        <?php
        $popular_query = "SELECT * FROM products WHERE subcategory = 'popular' ORDER BY created_at";
        $popular_result = mysqli_query($conn, $popular_query);
        
        $counter = 0;
        while ($popular_product = mysqli_fetch_assoc($popular_result)) {
            $stock_quantity = $popular_product['stock_quantity'];
            $is_sold_out = $stock_quantity == 0;

            $image = isset($popular_product['image']) && !empty($popular_product['image'])
                ? 'products/' . htmlspecialchars($popular_product['image'])
                : 'images/default-image.jpg';

            $product_name = htmlspecialchars($popular_product['product_name']);
            $product_price = htmlspecialchars($popular_product['price']);

            if ($counter % 4 == 0) {
                echo $counter == 0 ? '<div class="carousel-item active">' : '<div class="carousel-item">';
                echo '<div class="row row-cols-1 row-cols-md-4 g-4">';
            }
        ?>
            <div class="col">
                <div class="card h-100 text-center shadow-sm border-0 rounded product-card">
                    <div class="image-container position-relative overflow-hidden">
                        <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3"
                            alt="<?php echo $product_name; ?>"
                            style="height: 200px; object-fit: contain; transition: transform 0.3s ease-in-out;">
                        <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease-in-out;">
                            <?php if (!$is_sold_out): ?>
                                <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                    <input type="hidden" name="product_id" value="<?php echo $popular_product['product_id']; ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-outline-light btn-sm">
                                        <i class="fa fa-cart-plus"></i>
                                    </button>
                                    <a href="product_details.php?product_id=<?php echo $popular_product['product_id']; ?>" class="btn btn-light btn-sm">
                                        <i class="fa fa-info-circle"></i>
                                    </a>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>
                        <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
                        <?php if ($is_sold_out): ?>
                            <p class="text-danger fw-bold">Sold Out</p>
                           
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
            $counter++;
            if ($counter % 4 == 0 || $counter == mysqli_num_rows($popular_result)) {
                echo '</div></div>';
            }
        }
        ?>
    </div>

    <!-- Carousel Controls for Popular Products -->
    <button class="carousel-control-prev1" type="button" data-bs-target="#popularProductsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next1" type="button" data-bs-target="#popularProductsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Carousel for Latest Products -->
<div id="latestProductsCarousel" class="carousel slide" data-bs-ride="false" style="display: none;">
    <div class="carousel-inner">
        <?php
        $latest_query = "SELECT * FROM products WHERE subcategory = 'latest' ORDER BY created_at";
        $latest_result = mysqli_query($conn, $latest_query);

        $counter = 0;
        while ($latest_product = mysqli_fetch_assoc($latest_result)) {
            $stock_quantity = $latest_product['stock_quantity'];
            $is_sold_out = $stock_quantity == 0;

            $image = isset($latest_product['image']) && !empty($latest_product['image'])
                ? 'products/' . htmlspecialchars($latest_product['image'])
                : 'images/default-image.jpg';

            $product_name = htmlspecialchars($latest_product['product_name']);
            $product_price = htmlspecialchars($latest_product['price']);

            if ($counter % 4 == 0) {
                echo $counter == 0 ? '<div class="carousel-item active">' : '<div class="carousel-item">';
                echo '<div class="row row-cols-1 row-cols-md-4 g-4">';
            }
        ?>
            <div class="col">
                <div class="card h-100 text-center shadow-sm border-0 rounded product-card">
                    <div class="image-container position-relative overflow-hidden">
                        <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3"
                            alt="<?php echo $product_name; ?>"
                            style="height: 200px; object-fit: contain; transition: transform 0.3s ease-in-out;">
                        <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease-in-out;">
                            <?php if (!$is_sold_out): ?>
                                <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                    <input type="hidden" name="product_id" value="<?php echo $latest_product['product_id']; ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-outline-light btn-sm">
                                        <i class="fa fa-cart-plus"></i>
                                    </button>
                                    <a href="product_details.php?product_id=<?php echo $latest_product['product_id']; ?>" class="btn btn-light btn-sm">
                                        <i class="fa fa-info-circle"></i>
                                    </a>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>
                        <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
                        <?php if ($is_sold_out): ?>
                            <p class="text-danger fw-bold">Sold Out</p>
                            <button class="btn btn-outline-secondary btn-sm" disabled>Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
            $counter++;
            if ($counter % 4 == 0 || $counter == mysqli_num_rows($latest_result)) {
                echo '</div></div>';
            }
        }
        ?>
    </div>

    <!-- Carousel Controls for Latest Products -->
    <button class="carousel-control-prev1" type="button" data-bs-target="#latestProductsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next1" type="button" data-bs-target="#latestProductsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<script>
    function showPopular() {
        document.getElementById("popularProductsCarousel").style.display = "block";
        document.getElementById("latestProductsCarousel").style.display = "none";
        document.getElementById("popularBtn").classList.add("btn-dark");
        document.getElementById("popularBtn").classList.remove("btn-outline-dark");
        document.getElementById("latestBtn").classList.remove("btn-dark");
        document.getElementById("latestBtn").classList.add("btn-outline-dark");
    }

    function showLatest() {
        document.getElementById("popularProductsCarousel").style.display = "none";
        document.getElementById("latestProductsCarousel").style.display = "block";
        document.getElementById("latestBtn").classList.add("btn-dark");
        document.getElementById("latestBtn").classList.remove("btn-outline-dark");
        document.getElementById("popularBtn").classList.remove("btn-dark");
        document.getElementById("popularBtn").classList.add("btn-outline-dark");
    }
</script>


        <h2 class="mb-4 mt-5" style="font-family: 'Roboto', sans-serif; font-weight: 500;">Featured Products</h2>
        <div id="featuredProductsCarousel" class="carousel slide" data-bs-ride="false">
            <div class="carousel-inner">
                <?php
                $featured_query = "SELECT * FROM products WHERE subcategory = 'featured' ORDER BY created_at";
                $featured_result = mysqli_query($conn, $featured_query);

                $counter = 0;
                while ($featured_product = mysqli_fetch_assoc($featured_result)) {
                    $stock_quantity = $featured_product['stock_quantity'];
                    $is_sold_out = $stock_quantity == 0;

                    $image = isset($featured_product['image']) && !empty($featured_product['image'])
                        ? 'products/' . htmlspecialchars($featured_product['image'])
                        : 'images/default-image.jpg';

                    $product_name = htmlspecialchars($featured_product['product_name']);
                    $product_price = htmlspecialchars($featured_product['price']);

                    // Start a new carousel item every 4 products
                    if ($counter % 4 == 0) {
                        echo $counter == 0 ? '<div class="carousel-item active">' : '<div class="carousel-item">';
                        echo '<div class="row row-cols-1 row-cols-md-4 g-4">';
                    }
                ?>
                    <div class="col">
                        <div class="card h-100 text-center shadow-sm border-0 rounded product-card">
                            <div class="image-container position-relative overflow-hidden">
                                <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3"
                                    alt="<?php echo $product_name; ?>"
                                    style="height: 200px; object-fit: contain; transition: transform 0.3s ease-in-out;">
                                <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease-in-out;">
                                    <?php if (!$is_sold_out): ?>
                                        <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                            <input type="hidden" name="product_id" value="<?php echo $featured_product['product_id']; ?>">
                                            <button type="submit" name="add_to_cart" class="btn btn-outline-light btn-sm">
                                                <i class="fa fa-cart-plus"></i>
                                            </button>
                                            <a href="product_details.php?product_id=<?php echo $featured_product['product_id']; ?>" class="btn btn-light btn-sm">
                                                <i class="fa fa-info-circle"></i>
                                            </a>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>
                                <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
                                <?php if ($is_sold_out): ?>
                                    <p class="text-danger fw-bold">Sold Out</p>
                                    <button class="btn btn-outline-secondary btn-sm" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php
                    $counter++;
                    // Close the row and carousel item every 4 products
                    if ($counter % 4 == 0 || $counter == mysqli_num_rows($featured_result)) {
                        echo '</div></div>';
                    }
                }
                ?>
            </div>
            <!-- Carousel Controls -->
            <button class="carousel-control-prev1" type="button" data-bs-target="#featuredProductsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next1" type="button" data-bs-target="#featuredProductsCarousel" data-bs-slide="next ">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <style>
    .carousel-control-prev1,
    .carousel-control-next1 {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 5;
        background-color: transparent; /* Set background to transparent */
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid white; /* Optional: Add black border around buttons */
    }

    /* Left arrow positioning */
    .carousel-control-prev1 {
        left: -60px;
        /* Adjust as needed */
    }

    /* Right arrow positioning */
    .carousel-control-next1 {
        right: -60px;
        /* Adjust as needed */
    }

    /* Customize the arrow icons inside the buttons */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
       
        filter:invert(1);
    }
</style>



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