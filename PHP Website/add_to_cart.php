<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3307;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// DEBUG: Check if session user_id is set and print it (for testing only)
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    error_log("User ID is not set or empty in session. Session contents: " . print_r($_SESSION, true));
    $_SESSION['error'] = "Please log in to add items to your cart.";
    header("Location: user_login.php");
    exit;
}

// Store the user_id from the session
$user_id = $_SESSION['user_id'];

// Check if the request method is POST and required fields are set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch product details from the database
    $sql = "SELECT name, price, image FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $product_name = $product['name'];
        $product_price = $product['price'];
        $product_image = $product['image'];

        // Check if the product is already in the user's cart
        $sql = "SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the quantity if the product exists in the cart
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $quantity;
            $sql = "UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
            $stmt->execute();
        } else {
            // Insert new item into the cart
            $sql = "INSERT INTO cart_items (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $stmt->execute();
        }

        $_SESSION['success'] = "Product added to cart successfully!";
    } else {
        $_SESSION['error'] = "Product not found.";
    }
} else {
    $_SESSION['error'] = "Invalid request. Please try again.";
}

// Redirect back to the previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>


<!doctype html>
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
                            <li><a class="dropdown-item" href="orders.php">Orders</a></li>
                            <li><a class="dropdown-item" href="edit_profile.php">Edit Profile</a></li>
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
            <a href="cart.php" class="btn cart-btn" id="cart-button">
            <img src="./images/cart-icon.jpg" alt="Cart" style="width:20px; height:20px; margin-right:6px;">
            Cart (<span id="cart-count"><?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?></span>)
            </a>
        </div>
    </div>
</nav>

    <!-- Main Content -->
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <h1>Product Added to Cart</h1>
        <p><a href="products.php">Continue Shopping</a> or <a href="add_to_cart.php">View Cart</a></p>
    </div>
</body>
</html>
