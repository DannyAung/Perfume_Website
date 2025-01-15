<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You must be registered or logged in to add items to your cart.";
    exit;
}

$user_id = $_SESSION['user_id'];

$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Validate if the product exists in the database
    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Error: Product does not exist.";
        exit;
    }

    $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] > 0 ? $_POST['quantity'] : 1; // Default to 1 if invalid

    // Check if the item is already in the cart
    $sql = "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ? AND ordered_status = 'not_ordered'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the quantity if the item is already in the cart
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;

        $sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
        $update_stmt = $conn->prepare($sql);
        $update_stmt->bind_param("ii", $new_quantity, $cart_item['cart_item_id']);
        $update_stmt->execute();
    } else {
        // Insert the product into the cart if not already added
        $sql = "INSERT INTO cart_items (user_id, product_id, quantity, ordered_status) VALUES (?, ?, ?, 'not_ordered')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
        echo "Product added to your cart!";
    }

    // Redirect back to the previous page (or default page)
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'user_index.php';
    header("Location: " . $referer);
    exit;
}


// Increase Quantity Logic
if (isset($_POST['increase_quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];

    // Fetch current quantity
    $sql = "SELECT quantity FROM cart_items WHERE cart_item_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_item_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + 1;

        // Update quantity
        $sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
        $update_stmt = $conn->prepare($sql);
        $update_stmt->bind_param("ii", $new_quantity, $cart_item_id);
        $update_stmt->execute();
    }

    // Redirect to avoid repeated submission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Decrease Quantity Logic
if (isset($_POST['decrease_quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];

    // Fetch current quantity
    $sql = "SELECT quantity FROM cart_items WHERE cart_item_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_item_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $cart_item = $result->fetch_assoc();
        if ($cart_item['quantity'] > 1) {
            $new_quantity = $cart_item['quantity'] - 1;
            // Update quantity
            $sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
            $update_stmt = $conn->prepare($sql);
            $update_stmt->bind_param("ii", $new_quantity, $cart_item_id);
            $update_stmt->execute();
        } else {
            // Remove item from cart if quantity is 1
            $sql = "DELETE FROM cart_items WHERE cart_item_id = ?";
            $delete_stmt = $conn->prepare($sql);
            $delete_stmt->bind_param("i", $cart_item_id);
            $delete_stmt->execute();
        }
    }

    // Redirect to avoid repeated submission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Remove All Items Logic
if (isset($_POST['remove_all'])) {
    $sql = "DELETE FROM cart_items WHERE user_id = ? AND ordered_status = 'not_ordered'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Redirect to avoid repeated submission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
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
    <style>

        h1 {
            text-align: center;
            padding: 30px;
            background-color: #34495e;
            color: #ecf0f1;
            margin: 0;
        }

        .cart-container {
            width: 100%;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f2f2f2;
        }

        .cart-item img {
            width: 120px;
            height: auto;
            border-radius: 8px;
        }

        .product-details {
            flex-grow: 1;
            padding-left: 20px;
        }

        .product-details h3 {
            margin: 0;
            font-size: 1.1em;
            color: #2c3e50;
        }

        .quantity {
            display: flex;
            align-items: center;
        }

        .quantity button {
            background-color: #3498db;
            color: #ffffff;
            padding: 8px 12px;
            font-size: 1.1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .quantity button:hover {
            background-color: #2980b9;
        }

        .quantity input {
            width: 50px;
            padding: 5px;
            text-align: center;
            font-size: 1em;
            margin: 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .cart-item button {
            padding: 8px 16px;
            background-color: rgb(88, 148, 198);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cart-item button:hover {
            background-color: rgb(43, 132, 192);
        }

        .total {
            text-align: right;
            font-size: 1.5em;
            margin-top: 20px;
            color: #2c3e50;
        }

        .empty-cart {
            text-align: center;
            padding: 30px;
            color: rgb(129, 127, 244);
        }

        /* Styling for the cart item */
        .cart-item {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .product-details {
            flex-grow: 1;
        }

        .price {
            font-weight: bold;
            color: rgb(46, 148, 237);
        }

        /* Styling for the button container */
        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        /* Button Styles */
        .btn-danger,
        .btn-success {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-danger {
            background-color: #e74c3c;
            /* Red background for 'Remove All Items' */
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            /* Darker red on hover */
        }

        .btn-success {
            background-color: #28a745;
            /* Green background for 'Checkout' */
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
            /* Darker green on hover */
        }


        .original-price {
            text-decoration: line-through;
            color: blue;
            /* Optional: Use a lighter color to de-emphasize the original price */
        }

        .discounted-price {
            font-weight: bold;
            color: rgb(222, 31, 24);
            /* Optional: Use a distinct color (e.g., red) for the discounted price */
        }
    </style>
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
                    <a href="user_login.php" class="btn login-btn me-3">Login/Register</a>
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


 <!-- Breadcrumb Navigation -->
 <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add To Cart</li>
            </ol>
        </div>

   </nav>
   
   <div class="cart-page container my-2">
    <!-- Cart Header -->
    <div class="cart-header text-center mb-2">
        <h1>Your Shopping Cart</h1>
    </div>

    <div class="cart-container">
        <?php
        $sql = "SELECT ci.cart_item_id, ci.quantity, p.product_name, p.price, p.discounted_price, p.image, p.size
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.product_id
                WHERE ci.user_id = ? AND ci.ordered_status = 'not_ordered'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $total_price = 0;
            while ($item = $result->fetch_assoc()) {
                $regular_price = $item['price'];
                $discounted_price = $item['discounted_price'] > 0 ? $item['discounted_price'] : 0;
                $item_price = $discounted_price > 0 ? $discounted_price : $regular_price;
                $item_total = $item_price * $item['quantity'];
                $total_price += $item_total;

                $product_name = htmlspecialchars($item['product_name']);
                $product_image = htmlspecialchars($item['image']);
                $product_size = htmlspecialchars($item['size']); // Fetch the size
                $image_path = "products/" . $product_image;

                // Render cart item with a modern layout
                echo "<div class='cart-item d-flex align-items-center p-3 mb-4 bg-light rounded shadow-sm'>
                    <img src='" . $image_path . "' alt='" . $product_name . "' class='product-image img-thumbnail me-4' style='width: 100px; height: 100px; object-fit: cover;'>
                    <div class='product-details flex-grow-1'>
                        <h4>" . $product_name . "</h4>
                        <p class='text-muted'>Size: " . $product_size . "</p>";

                // Display price with discount if applicable
                if ($discounted_price > 0) {
                    echo "<p class='regular-price text-muted'><del>$" . number_format($regular_price, 2) . "</del></p>";
                    echo "<p class='discounted-price text-success fw-bold'>$" . number_format($discounted_price, 2) . "</p>";
                } else {
                    echo "<p class='price fw-bold'>$" . number_format($regular_price, 2) . "</p>";
                }

                echo "<p>Quantity: " . $item['quantity'] . "</p>
                    </div>
                    <div class='quantity-controls ms-3'>
                        <form method='post'>
                            <input type='hidden' name='cart_item_id' value='" . $item['cart_item_id'] . "'>
                            <button type='submit' name='decrease_quantity' class='btn btn-sm btn-outline-secondary' >&ndash;</button>
                            <button type='submit' name='increase_quantity' class='btn btn-sm btn-outline-primary'>+</button>
                        </form>
                    </div>
                </div>";
            }

            // Display total price at the bottom
            echo "<div class='total-price p-3 bg-white text-center text-black fw-bold rounded'>
                    Total Price: $" . number_format($total_price, 2) . "
                  </div>";

            // Button Container (remove all, proceed to checkout)
            echo "<div class='button-container d-flex justify-content-center mt-4'>
                    <form method='post' class='w-20'>
                        <button type='submit' name='remove_all' class='btn btn-danger w-100'>Remove All Items</button>
                    </form>
                    <form method='post' action='checkout.php' class='w-20'>
                        <button type='submit' name='check_out' class='btn btn-success w-100'>Proceed to Checkout</button>
                    </form>
                  </div>";
        } else {
            echo "<div class='empty-cart text-center py-5'>
                    <p class='text-muted'>Your cart is empty.</p>
                    <a href='user_index.php' class='btn btn-primary mt-3'>Continue Shopping</a>
                  </div>";
        }
        ?>
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

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>