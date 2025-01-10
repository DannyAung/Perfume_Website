<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to check out.";
    exit;
}

$user_id = $_SESSION['user_id'];

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

// Retrieve cart items
$sql = "SELECT ci.cart_item_id, ci.quantity, p.product_id, p.product_name, p.price, p.discounted_price, p.image, p.size
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.user_id = ? AND ci.ordered_status = 'not_ordered'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
$cart_items = [];
$discount_percentage = 0; // Default discount percentage

while ($item = $result->fetch_assoc()) {
    $regular_price = $item['price'];
    $discounted_price = $item['discounted_price'] > 0 ? $item['discounted_price'] : 0;
    $item_price = $discounted_price > 0 ? $discounted_price : $regular_price;
    $item_total = $item_price * $item['quantity'];
    $total_price += $item_total;
    $cart_items[] = $item;
}

// Initialize coupon session variables
$discount_percentage = $_SESSION['discount_percentage'] ?? 0;
$applied_coupon_code = $_SESSION['applied_coupon_code'] ?? "N/A";

// Handle manual coupon application only when the user submits it
if (isset($_POST['apply_coupon'])) {
    $manual_coupon_code = trim($_POST['coupon_code']); // Get the submitted coupon code

    // Only allow coupon if the order total is above $300
    if ($total_price < 300) {
        echo "<script>alert('Coupons can only be applied to orders above $300.');</script>";
    } else {
        // Check if the coupon code exists and is active
        $coupon_sql = "SELECT discount_percentage FROM coupons WHERE coupon_code = ? AND is_active = 1";
        $coupon_stmt = $conn->prepare($coupon_sql);
        $coupon_stmt->bind_param("s", $manual_coupon_code);
        $coupon_stmt->execute();
        $coupon_result = $coupon_stmt->get_result();

        if ($coupon_result->num_rows > 0) {
            // Apply the coupon and store it in the session
            $coupon_data = $coupon_result->fetch_assoc();
            $discount_percentage = $coupon_data['discount_percentage'];
            $_SESSION['discount_percentage'] = $discount_percentage;
            $_SESSION['applied_coupon_code'] = $manual_coupon_code;
            echo "<script>alert('Coupon applied successfully!');</script>";
        } else {
            echo "<script>alert('Invalid or expired coupon code!');</script>";
        }
    }
}

// Calculate final price with discount
$final_price = $total_price - ($total_price * ($discount_percentage / 100));

// Apply shipping fee
$shipping_fee = 0;
if (isset($_POST['shipping_method'])) {
    $shipping_method = $_POST['shipping_method'];
    $shipping_fee = ($shipping_method === "express") ? 40 : 20; // Adjust fees based on the selected method
}

// Calculate final total price
$final_total_price = $final_price + $shipping_fee;

// Handle checkout process
if (isset($_POST['checkout'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $payment_method = $_POST['payment_method'];
    $shipping_method = $_POST['shipping_method'];

    // Get the coupon code and discount percentage
    $coupon_code = $_SESSION['applied_coupon_code'] ?? null;
    $discount_percentage = $_SESSION['discount_percentage'] ?? 0;

    // Update user's address and phone number
    $update_sql = "UPDATE users SET address = ?, phone_number = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $address, $phone, $user_id);
    $update_stmt->execute();

    // Insert order into the orders table, including the coupon code and discount percentage
    $order_sql = "INSERT INTO orders (user_id, total_price, name, address, phone, email, payment_method, shipping_method, shipping_fee, coupon_code, discount_percentage) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("idssssssdsd", $user_id, $final_total_price, $name, $address, $phone, $email, $payment_method, $shipping_method, $shipping_fee, $coupon_code, $discount_percentage);

    if ($order_stmt->execute()) {
        $order_id = $conn->insert_id;

        // Insert order items into the order_items table
        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['discounted_price'] > 0 ? $item['discounted_price'] : $item['price']; // Use the discounted price if available

            $order_item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, size) 
                               VALUES (?, ?, ?, ?, ?, ?)";
            $order_item_stmt = $conn->prepare($order_item_sql);
            $order_item_stmt->bind_param("iisids", $order_id, $product_id, $item['product_name'], $quantity, $price, $item['size']);
            $order_item_stmt->execute();

            // Update stock quantity
            $update_stock_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $update_stock_stmt = $conn->prepare($update_stock_sql);
            $update_stock_stmt->bind_param("ii", $quantity, $product_id);
            $update_stock_stmt->execute();
        }

        // Update cart items status to 'ordered' after the checkout process
        $update_cart_sql = "UPDATE cart_items SET ordered_status = 'ordered' WHERE user_id = ?";
        $update_cart_stmt = $conn->prepare($update_cart_sql);
        $update_cart_stmt->bind_param("i", $user_id);
        $update_cart_stmt->execute();

        // Clear coupon session variables after the order is placed
        unset($_SESSION['discount_percentage']);
        unset($_SESSION['applied_coupon_code']);
    
        // Redirect to receipt page
        header("Location: receipt.php?order_id=" . $order_id);
        exit();
    } else {
        die("Error processing order: " . $order_stmt->error);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
        }

        .checkout-container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            padding-bottom: 20px;
        }

        .cart-item-summary {
            margin-bottom: 20px;
        }

        .total-price {
            text-align: right;
            font-size: 1.5em;
            margin-top: 20px;
        }

        .checkout-form input {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="checkout-container mt-0">
        <h1>Checkout</h1>
        <!-- Cart Items Summary -->
        <div class="cart-item-summary">
            <h3>Cart Summary</h3>
            <?php
            if (!empty($cart_items)) {
                foreach ($cart_items as $item) {
                    $regular_price = $item['price'];
                    $discounted_price = $item['discounted_price'] > 0 ? $item['discounted_price'] : 0;
                    $item_price = $discounted_price > 0 ? $discounted_price : $regular_price;
                    $image_path = "products/" . htmlspecialchars($item['image']);
                    echo "<div class='cart-item'>
                        <img src='" . $image_path . "' alt='" . htmlspecialchars($item['product_name']) . "' class='product-image' width='100'>
                        <div class='product-details'>
                            <p><strong>" . htmlspecialchars($item['product_name']) . "</strong></p>";
                    if ($discounted_price > 0) {
                        echo "<p class='text-muted'><del>$" . number_format($regular_price, 2) . "</del></p>";
                        echo "<p><strong>$" . number_format($discounted_price, 2) . "</strong></p>";
                    } else {
                        echo "<p><strong>$" . number_format($regular_price, 2) . "</strong></p>";
                    }
                    echo "<p>Quantity: " . $item['quantity'] . " <span class='text-muted'>Size: " . htmlspecialchars($item['size']) . "</span></p>
                    </div>
                    </div>";
                }
            }
            ?>
        </div>

        <!-- Coupon Application -->
        <form method="POST" action="checkout.php">
            <label for="coupon_code">Enter Coupon Code:</label>
            <input type="text" name="coupon_code" id="coupon_code" value="<?php echo htmlspecialchars($applied_coupon_code !== 'N/A' ? $applied_coupon_code : ''); ?>">
            <button type="submit" name="apply_coupon">Apply Coupon</button>
        </form>
        <p>Submitted Coupon Code: <?php echo htmlspecialchars($applied_coupon_code); ?></p>

        <form method="post" class="checkout-form">
            <h3>Shipping Details</h3>
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Shipping Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <!-- Payment Method -->
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="" selected disabled>Select Payment Method</option>
                    <option value="kpay">K Pay</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                </select>
            </div>

            <!-- Shipping Method -->
            <div class="mb-3">
                <label for="shipping_method" class="form-label">Shipping Method</label>
                <select class="form-control" id="shipping_method" name="shipping_method" onchange="updateTotalPrice()" required>
                    <option value="" disabled selected>Select Shipping Method</option>
                    <option value="standard">Standard - $20</option>
                    <option value="express">Express - $40</option>
                </select>
            </div>

            <div class="total-price">
                <p id="total-amount">Total Amount: $<?php echo number_format($total_price, 2); ?></p>
                <p id="coupon-discount"><?php if ($discount_percentage > 0) echo "Coupon Applied: -$" . number_format($total_price * ($discount_percentage / 100), 2); ?></p>
                <p id="shipping-fee">Shipping Fee: $<?php echo number_format($shipping_fee, 2); ?></p>
                <hr>
                <p id="final-total"><strong>Total Amount: $<?php echo number_format($final_total_price, 2); ?></strong></p>
            </div>

            <button type="submit" name="checkout" class="btn btn-success btn-lg w-100">Confirm and Checkout</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
<script>
    const baseTotalPrice = <?php echo $total_price; ?>;
    const discountPercentage = <?php echo $discount_percentage; ?>;
    let shippingFee = <?php echo $shipping_fee; ?>;
    let finalTotalPrice = baseTotalPrice - (baseTotalPrice * (discountPercentage / 100)) + shippingFee;

    function updateTotalPrice() {
        const shippingMethod = document.getElementById('shipping_method').value;
        shippingFee = shippingMethod === 'express' ? 40 : 20; // Assign correct shipping fee based on selection
        finalTotalPrice = baseTotalPrice - (baseTotalPrice * (discountPercentage / 100)) + shippingFee;

        // Update displayed prices dynamically
        document.getElementById('total-amount').innerText = "Total Amount: $" + baseTotalPrice.toFixed(2);
        document.getElementById('coupon-discount').innerText = discountPercentage > 0 ? "Coupon Applied: -$" + (baseTotalPrice * (discountPercentage / 100)).toFixed(2) : '';
        document.getElementById('shipping-fee').innerText = "Shipping Fee: $" + shippingFee.toFixed(2);
        document.getElementById('final-total').innerText = "Total Amount: $" + finalTotalPrice.toFixed(2);
    }
</script>

<?php
$conn->close();
?>