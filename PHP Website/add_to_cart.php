<?php
session_start();

// Assuming user is logged in and their user_id is stored in session
if (!isset($_SESSION['user_id'])) {
    echo "You must be register or logged in to add items to your cart.";
    exit;
}

$user_id = $_SESSION['user_id']; // User ID from session

// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

// Add to Cart Logic
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Check if quantity is set and is a valid number
    $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] > 0 ? $_POST['quantity'] : 1; // Default to 1 if invalid
    
    // Check if product already exists in the cart for the user
    $sql = "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ? AND ordered_status = 'not_ordered'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Product already exists in the cart, update the quantity
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;

        $sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
        $update_stmt = $conn->prepare($sql);
        $update_stmt->bind_param("ii", $new_quantity, $cart_item['cart_item_id']);
        $update_stmt->execute();
    } else {
        // Insert the product into the cart
        $sql = "INSERT INTO cart_items (user_id, product_id, quantity, ordered_status) VALUES (?, ?, ?, 'not_ordered')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
        echo "Product added to your cart!";
    }

    // Redirect to avoid repeated submission
    header("Location: user_index.php");
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
    <title>Your Cart</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style for the page */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            padding: 30px;
            background-color: #34495e;
            color: #ecf0f1;
            margin: 0;
        }
        .cart-container {
            width: 80%;
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
            background-color:rgb(88, 148, 198);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .cart-item button:hover {
            background-color:rgb(43, 132, 192);
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
            color:rgb(129, 127, 244);
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
    color:rgb(46, 148, 237);
}

/* Styling for the button container */
.button-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
}

/* Button Styles */
.btn-danger, .btn-success {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-danger {
    background-color: #e74c3c; /* Red background for 'Remove All Items' */
    color: white;
}

.btn-danger:hover {
    background-color: #c0392b; /* Darker red on hover */
}

.btn-success {
    background-color: #28a745; /* Green background for 'Checkout' */
    color: white;
}

.btn-success:hover {
    background-color: #218838; /* Darker green on hover */
}


.original-price {
    text-decoration: line-through;
    color: blue; /* Optional: Use a lighter color to de-emphasize the original price */
}

.discounted-price {
    font-weight: bold;
    color:rgb(222, 31, 24); /* Optional: Use a distinct color (e.g., red) for the discounted price */
}


    </style>
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

    <h1>Your Cart</h1>
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

        // Render cart item
        echo "<div class='cart-item'>
                <img src='" . $image_path . "' alt='" . $product_name . "' class='product-image'>
                <div class='product-details'>
                    <h3>" . $product_name . "</h3>";

        // Display size
        echo "<p>Size: " . $product_size . "</p>";

        // Show regular price with a strikethrough if there is a discounted price
        if ($discounted_price > 0) {
            echo "<p class='regular-price'><del>$" . number_format($regular_price, 2) . "</del></p>";
            echo "<p class='discounted-price'>$" . number_format($discounted_price, 2) . "</p>";
        } else {
            echo "<p class='price'>$" . number_format($regular_price, 2) . "</p>";
        }

        echo "<p>Quantity: " . $item['quantity'] . "</p>
                </div>
                <form method='post' class='d-inline'>
                    <input type='hidden' name='cart_item_id' value='" . $item['cart_item_id'] . "'>
                    <button type='submit' name='decrease_quantity' class='btn btn-secondary'>-</button>
                    <button type='submit' name='increase_quantity' class='btn btn-primary'>+</button>
                </form>
            </div>";
        }

        // Display total price
        echo "<div class='total'>Total Price: $" . number_format($total_price, 2) . "</div>";

        // Button Container
        echo "<div class='button-container'>
            <form method='post'>
                <button type='submit' name='remove_all' class='btn btn-danger'>Remove All Items</button>
            </form>
            <form method='post' action='checkout.php'>
                <button type='submit' name='check_out' class='btn btn-success'>Process to Check Out</button>
            </form>
        </div>";
        } else {
        echo "<div class='empty-cart'><p>Your cart is empty.</p></div>";
        echo "<a href='user_index.php'><p>Continue Shopping?</p></a>";
        }

    ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>