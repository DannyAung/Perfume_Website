<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You must be registered or logged in to add items to your cart.";
    exit;
}
if (isset($_POST['check_out'])) {
   
    $_SESSION['checkout_started'] = true;

   
    header("Location: checkout.php"); 
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

    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<script>alert('Error: Product does not exist.');</script>";
        exit;
    }

    $product = $result->fetch_assoc();
    $stock_quantity = $product['stock_quantity'];
    $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) && $_POST['quantity'] > 0 ? $_POST['quantity'] : 1; // Default to 1 if invalid

    $sql = "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ? AND ordered_status = 'not_ordered'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;

        if ($new_quantity > $stock_quantity) {
            echo "<script>alert('Error: Cannot add more than available stock.');</script>";
            exit;
        }

        $sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
        $update_stmt = $conn->prepare($sql);
        $update_stmt->bind_param("ii", $new_quantity, $cart_item['cart_item_id']);
        $update_stmt->execute();
    } else {
        if ($quantity > $stock_quantity) {
            echo "<script>alert('Error: Cannot add more than available stock.');</script>";
            exit;
        }

        $sql = "INSERT INTO cart_items (user_id, product_id, quantity, ordered_status) VALUES (?, ?, ?, 'not_ordered')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $stmt->execute();
    }

    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'user_index.php';
    echo "<script>window.location.href = '$referer';</script>";
    exit;
}


if (isset($_POST['increase_quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];

    // Fetch current quantity and stock quantity
    $sql = "SELECT ci.quantity, p.stock_quantity FROM cart_items ci JOIN products p ON ci.product_id = p.product_id WHERE ci.cart_item_id = ? AND ci.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_item_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $cart_item = $result->fetch_assoc();
        $current_quantity = $cart_item['quantity'];
        $stock_quantity = $cart_item['stock_quantity'];

        if ($current_quantity < $stock_quantity) {
            $new_quantity = $current_quantity + 1;

        
            $sql = "UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?";
            $update_stmt = $conn->prepare($sql);
            $update_stmt->bind_param("ii", $new_quantity, $cart_item_id);
            $update_stmt->execute();
        } else {
            echo "<script>alert('Error: Cannot increase quantity. Only $stock_quantity items in stock.');</script>";
        }
    }

 
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if (isset($_POST['decrease_quantity'])) {
    $cart_item_id = $_POST['cart_item_id'];

  
    $sql = "SELECT quantity FROM cart_items WHERE cart_item_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_item_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $cart_item = $result->fetch_assoc();
        if ($cart_item['quantity'] > 1) {
            $new_quantity = $cart_item['quantity'] - 1;        
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

   
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Remove All Items Logic
if (isset($_POST['remove_all'])) {
    $sql = "DELETE FROM cart_items WHERE user_id = ? AND ordered_status = 'not_ordered'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

   
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
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
           
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }


        .original-price {
            text-decoration: line-through;
            color: blue;
         
        }

        .discounted-price {
            font-weight: bold;
            color: rgb(222, 31, 24);
           
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>


    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Add To Cart</li>
            </ol>
        </div>

    </nav>

    <div class="cart-page container my-2">
      
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

                   
                    echo "<div class='cart-item d-flex align-items-center p-3 mb-4 bg-light rounded shadow-sm'>
                    <img src='" . $image_path . "' alt='" . $product_name . "' class='product-image img-thumbnail me-4' style='width: 100px; height: 100px; object-fit: cover;'>
                    <div class='product-details flex-grow-1'>
                        <h4>" . $product_name . "</h4>
                        <p class='text-muted'>Size: " . $product_size . "</p>";

                  
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

               
                echo "<div class='total-price p-3 bg-white text-center text-black fw-bold rounded'>
                    Total Price: $" . number_format($total_price, 2) . "
                  </div>";

            
                echo "<div class='button-container d-flex justify-content-center mt-4'>
                    <form method='post' class='w-20'>
                        <button type='submit' name='remove_all' class='btn btn-danger w-100'>Remove All Items</button>
                    </form>
                    <form method='post' action='$_SERVER[PHP_SELF]' class='w-20'>
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
    <?php include 'footer.php'; ?>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>