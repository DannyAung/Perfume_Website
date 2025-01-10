<?php
if (!isset($_SESSION)) {
    session_start();
}

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
   
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</head>

<body style="background-color:rgb(249, 249, 249);">
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

    <!-- New Navigation Links Section -->
    <div class="home py-1">
        <div class="container">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" href="user_index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Category
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li><a class="dropdown-item" href="men_category.php">Men</a></li>
                        <li><a class="dropdown-item" href="women_category.php">Women</a></li>
                        <li><a class="dropdown-item" href="unisex_category.php">Unisex</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" id="deliveryLink">Delivery</a>
                    <div class="delivery-tooltip" id="deliveryTooltip">
                        <p><b>Delivery: Within 2 or 3 days for YGN</b></p>
                        <p><b>Delivery: Within 2 or 5 days for Other Locations</b></p>
                    </div>
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

                    // Calculate discount percentage
                    $discount_percentage = 0;
                    if ($product_price > 0) {
                        $discount_percentage = round((($product_price - $product_discounted_price) / $product_price) * 100);
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
                                    <button class="btn btn-outline-dark btn-sm w-100" disabled style="opacity: 0.7; cursor: not-allowed;">
                                        Out of Stock
                                    </button>
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
            <h2 class="text-center mb-5 mt-5" style="font-family: 'Roboto', sans-serif; font-weight: 600; color: #2c3e50;">Browse by Category</h2>

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Men Category -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded overflow-hidden">
                        <div class="position-relative">
                            <img src="images/men-category.jpg" class="card-img-top" alt="Men Products" style="height: 250px; object-fit: cover;">
                            <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <a href="men_category.php" class="btn btn-light btn-lg rounded-pill">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3" style="font-family: 'Roboto', sans-serif; font-weight: 600; color: #34495e;">Men</h5>
                            <p class="card-text text-muted">Explore our collection of men's products.</p>
                        </div>
                    </div>
                </div>

                <!-- Women Category -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded overflow-hidden">
                        <div class="position-relative">
                            <img src="images/women-category.jpg" class="card-img-top" alt="Women Products" style="height: 250px; object-fit: cover;">
                            <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <a href="women_category.php" class="btn btn-light btn-lg rounded-pill">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3" style="font-family: 'Roboto', sans-serif; font-weight: 600; color: #34495e;">Women</h5>
                            <p class="card-text text-muted">Discover elegant products for women.</p>
                        </div>
                    </div>
                </div>

                <!-- Unisex Category -->
                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded overflow-hidden">
                        <div class="position-relative">
                            <img src="images/unisex-category.jpg" class="card-img-top" alt="Unisex Products" style="height: 250px; object-fit: cover;">
                            <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <a href="unisex_category.php" class="btn btn-light btn-lg rounded-pill">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3" style="font-family: 'Roboto', sans-serif; font-weight: 600; color: #34495e;">Unisex</h5>
                            <p class="card-text text-muted">Browse unisex products suitable for everyone.</p>
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

        <!-- Related Resources Section -->
        <section class="related-resources py-4">
            <div class="container">
                <h3 class="text-center mb-4 display-5 font-weight-bold">Related Resources</h3>
                <div class="row">
                    <!-- Resource Card Template -->
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="images/resource1.jpg" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-between text-center">
                                <h5 class="card-title font-weight-bold">How to Spray Perfume</h5>
                                <p class="card-text text-muted">Learn the best ways to apply perfume throughout the day for lasting fragrance.</p>
                                <a href="how_to_spray_perfume.php" class="btn btn-gradient-primary w-100">Read More</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="images/resource2.jpg" class="card-img-top" >
                            <div class="card-body d-flex flex-column justify-content-between text-center">
                                <h5 class="card-title font-weight-bold">How to Find Your Signature Scent</h5>
                                <p class="card-text text-muted">A guide to help you find the perfect fragrance for your personality and lifestyle.</p>
                                <a href="choosing_right_perfume.php" class="btn btn-gradient-primary w-100">Read More</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="images/resource3.jpg" class="card-img-top" >
                            <div class="card-body d-flex flex-column justify-content-between text-center">
                                <h5 class="card-title font-weight-bold">Perfume Tips for Different Occasions</h5>
                                <p class="card-text text-muted">Tips on selecting the perfect scent for work, date, and more.</p>
                                <a href="perfume_tips_occasion.php" class="btn btn-gradient-primary w-100">Read More</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <img src="images/resource4.jpg" class="card-img-top" alt="Perfume Storage Guide">
                            <div class="card-body d-flex flex-column justify-content-between text-center">
                                <h5 class="card-title font-weight-bold">Perfume Storage Guide</h5>
                                <p class="card-text text-muted">How to store your perfumes properly to maintain their fragrance and quality.</p>
                                <a href="perfume_storage.php" class="btn btn-gradient-primary w-100">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">

                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">About Us</h5>
                    <p class="text-muted">Fragrance Haven is your ultimate destination for high-quality perfumes that elevate your senses. Explore our wide range of fragrances designed to suit every occasion and personality.</p>
                </div>


                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="user_index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="women_category.php" class="text-white text-decoration-none">Women’s Collection</a></li>
                        <li><a href="men_category.php" class="text-white text-decoration-none">Men’s Collection</a></li>
                        <a href="unisex_category.php" class="text-white text-decoration-none">Unisex Collection</a></li>
                        <li><a href="about_us.php" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="contact_us.php" class="text-white text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>


                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">Contact Info</h5>
                    <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> 123 Fragrance St, City, Country</p>
                    <p class="text-muted"><i class="fas fa-phone-alt me-2"></i> +123 456 7890</p>
                    <p class="text-muted"><i class="fas fa-envelope me-2"></i> support@fragrancehaven.com</p>
                </div>
            </div>


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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

</body>

</html>

<?php

mysqli_close($conn);
?>