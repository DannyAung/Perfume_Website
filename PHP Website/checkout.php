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

// Handle coupon application
if (isset($_POST['apply_coupon'])) {
    $manual_coupon_code = trim($_POST['coupon_code']); // Get the submitted coupon code

    // Validate the coupon from the `coupons` table
    $coupon_sql = "SELECT coupon_id, discount_percentage, minimum_purchase_amount, valid_from, valid_to 
                   FROM coupons 
                   WHERE coupon_code = ? AND valid_from <= NOW() AND valid_to >= NOW()";
    $coupon_stmt = $conn->prepare($coupon_sql);
    $coupon_stmt->bind_param("s", $manual_coupon_code);
    $coupon_stmt->execute();
    $coupon_result = $coupon_stmt->get_result();

    if ($coupon_result->num_rows > 0) {
        $coupon_data = $coupon_result->fetch_assoc();

        // Check if the order total meets the minimum purchase amount
        if ($total_price >= $coupon_data['minimum_purchase_amount']) {
            // Store coupon details in the session only when applied
            $_SESSION['discount_percentage'] = $coupon_data['discount_percentage'];
            $_SESSION['applied_coupon_code'] = $manual_coupon_code;
            $_SESSION['coupon_id'] = $coupon_data['coupon_id'];
            echo "<script>alert('Coupon applied successfully!');</script>";
        } else {
            echo "<script>alert('This coupon requires a minimum purchase of $" . $coupon_data['minimum_purchase_amount'] . ".');</script>";
        }
    } else {
        // Invalid coupon if not found or expired
        echo "<script>alert('Invalid or expired coupon code!');</script>";
    }
}

// Calculate final price with discount
$discount_amount = $total_price * ($discount_percentage / 100);
$final_price = $total_price - $discount_amount;

// Fetch shipping methods and fees from the database
$shipping_sql = "SELECT shipping_method, shipping_fee FROM shipping";
$shipping_result = $conn->query($shipping_sql);

$shipping_methods = [];
if ($shipping_result->num_rows > 0) {
    while ($row = $shipping_result->fetch_assoc()) {
        $shipping_methods[$row['shipping_method']] = $row['shipping_fee'];
    }
}

// Apply shipping fee
$shipping_fee = 0;
if (isset($_POST['shipping_method']) && !empty($_POST['shipping_method'])) {
    $shipping_method = $_POST['shipping_method'];
    // Fetch the shipping fee from the $shipping_methods array
    $shipping_fee = $shipping_methods[$shipping_method] ?? 0; // Default to 0 if the method is not found
}

// Calculate final total price
$final_total_price = $final_price + $shipping_fee;



// Handle the checkout process
if (isset($_POST['checkout'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $payment_method = $_POST['payment_method'];
    $shipping_method = $_POST['shipping_method'];

    // Get the coupon details from the session
    $coupon_code = $_SESSION['applied_coupon_code'] ?? null;
    $discount_percentage = $_SESSION['discount_percentage'] ?? 0;
    $coupon_id = $_SESSION['coupon_id'] ?? null;

    // Insert order into the orders table, including the coupon details
    $order_sql = "INSERT INTO orders (user_id, total_price, name, address, phone, email, payment_method, shipping_method, shipping_fee, discount_percentage, coupon_code, coupon_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("idssssssdssi", $user_id, $final_total_price, $name, $address, $phone, $email, $payment_method, $shipping_method, $shipping_fee, $discount_percentage, $coupon_code, $coupon_id);

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
        unset($_SESSION['coupon_id']);

        // Redirect to receipt page
        header("Location: receipt.php?order_id=" . $order_id);
        exit();
    } else {
        die("Error processing order: " . $order_stmt->error);
    }
}

// Before using $applied_coupon_code, check if it's set in the session
$applied_coupon_code = isset($_SESSION['applied_coupon_code']) ? $_SESSION['applied_coupon_code'] : "";

// Using htmlspecialchars safely to avoid deprecated behavior
$applied_coupon_code_html = htmlspecialchars($applied_coupon_code, ENT_QUOTES, 'UTF-8');

// Check if discount_percentage is set in the session, else default to 0
$discount_percentage = isset($_SESSION['discount_percentage']) ? $_SESSION['discount_percentage'] : 0;

// Regenerate session ID to prevent session hijacking and ensure the session data is reset
session_regenerate_id(true);
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
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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

        .product-image {
            border-radius: 5px;
            margin-right: 15px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .cart-item:hover {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        @media (max-width: 768px) {
            .checkout-container {
                width: 95%;
                padding: 20px;
            }

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .product-image {
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="add_to_cart.php">Your Cart</a></li>

                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </div>

    </nav>
    <div class="checkout-container">
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

        <!-- Coupon Application Form -->
        <form method="POST" action="checkout.php" onsubmit="return applyCoupon(event)">
            <label for="coupon_code">Enter Coupon Code:</label>
            <input type="text" name="coupon_code" id="coupon_code" class="form-control" value="<?php echo htmlspecialchars($applied_coupon_code); ?>">
            <button type="submit" name="apply_coupon" class="btn btn-primary">Apply Coupon</button>
        </form>

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
                <select class="form-control" id="payment_method" name="payment_method" required onchange="handlePaymentMethodChange()">
                    <option value="" selected disabled>Select Payment Method</option>
                    <option value="kpay">K Pay</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                </select>
            </div>

            <!-- Additional Fields -->
            <div id="kpay_fields" class="d-none">
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter your phone number" oninput="validatePhoneNumber()">
                    <div id="phone_number_error" class="text-danger d-none">Invalid Phone Number</div>
                </div>
                <div class="mb-3">
                    <label for="otp" class="form-label">OTP</label>
                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter OTP">
                </div>
                <button type="button" id="get_otp_button" class="btn btn-primary d-none" onclick="generateOTP()">Get OTP</button>
            </div>

            <div id="credit_card_fields" class="d-none">
                <div class="mb-3">
                    <label for="card_number" class="form-label">Card Number</label>
                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="Enter your credit card number">
                </div>
                <div class="mb-3">
                    <label for="expiry_date" class="form-label">Expiry Date</label>
                    <input type="month" class="form-control" id="expiry_date" name="expiry_date">
                </div>
                <div class="mb-3">
                    <label for="cvv" class="form-label">CVV</label>
                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="Enter CVV">
                </div>
            </div>

            <div id="cash_on_delivery_fields" class="d-none">
                <div class="mb-3">
                    <p>Cash on Delivery selected. No additional information needed.</p>
                </div>
            </div>

            <!-- JavaScript to Handle Payment Method Change -->
            <script>
                function handlePaymentMethodChange() {
                    const paymentMethod = document.getElementById('payment_method').value;

                    // Hide all additional fields initially
                    document.getElementById('kpay_fields').classList.add('d-none');
                    document.getElementById('credit_card_fields').classList.add('d-none');
                    document.getElementById('cash_on_delivery_fields').classList.add('d-none');

                    // Show fields based on the selected payment method
                    if (paymentMethod === 'kpay') {
                        document.getElementById('kpay_fields').classList.remove('d-none');
                    } else if (paymentMethod === 'credit_card') {
                        document.getElementById('credit_card_fields').classList.remove('d-none');
                    } else if (paymentMethod === 'cash_on_delivery') {
                        document.getElementById('cash_on_delivery_fields').classList.remove('d-none');
                    }
                }

                function validatePhoneNumber() {
                    const phoneNumber = document.getElementById('phone_number').value;
                    const phoneError = document.getElementById('phone_number_error');
                    const getOtpButton = document.getElementById('get_otp_button');

                    // Validate phone number (must be exactly 11 digits)
                    if (/^\d{11}$/.test(phoneNumber)) {
                        phoneError.classList.add('d-none'); // Hide error message
                        getOtpButton.classList.remove('d-none'); // Show "Get OTP" button
                    } else {
                        phoneError.classList.remove('d-none'); // Show error message
                        getOtpButton.classList.add('d-none'); // Hide "Get OTP" button
                    }
                }

                function generateOTP() {
                    // This function will handle OTP generation logic (e.g., sending OTP to the server)
                    alert('OTP has been sent to your phone number.');
                }
            </script>

           <!-- Shipping Method -->
<div class="mb-3">
    <label for="shipping_method" class="form-label">Shipping Method</label>
    <select class="form-control" id="shipping_method" name="shipping_method" onchange="updateTotalPrice()" required>
        <option value="" disabled selected>Select Shipping Method</option>
        <?php
        foreach ($shipping_methods as $method => $fee) {
            echo "<option value='" . htmlspecialchars($method) . "' data-fee='" . $fee . "'>"
                . htmlspecialchars($method) . " - $" . $fee . "</option>";
        }
        ?>
    </select>
</div>

<div class="total-price">
    <p id="total-amount">Subtotal: $<?php echo number_format($total_price, 2); ?></p> <!-- Display Subtotal -->

    <?php if ($discount_percentage > 0): ?>
        <p id="coupon-discount">Coupon Applied: -$<?php echo number_format($discount_amount, 2); ?></p> <!-- Display Coupon Discount -->
    <?php else: ?>
        <p id="coupon-discount"></p> <!-- Hide coupon discount if no coupon is applied -->
    <?php endif; ?>

    <p id="shipping-fee">Shipping Fee: $<?php echo number_format($shipping_fee, 2); ?></p> <!-- Display Shipping Fee -->

    <hr>

    <p id="final-total">
        <strong>Total Amount: $<?php
            // Final total calculation after applying the discount and adding the shipping fee
            $final_total_price = $total_price - $discount_amount + $shipping_fee;
            echo number_format($final_total_price, 2);
        ?></strong>
    </p> <!-- Display Final Total Amount -->
</div>

            <button type="submit" name="checkout" class="btn btn-success btn-lg w-100">Confirm and Checkout</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Get the current shipping fee from PHP variable
    let shippingFee = <?php echo json_encode($shipping_fee); ?>;
    const baseTotalPrice = <?php echo $total_price; ?>; // Base total price without shipping or discount
    const discountPercentage = <?php echo $discount_percentage; ?>; // Discount percentage (0 if no coupon)

    // Function to update total price dynamically
    function updateTotalPrice() {
        const shippingMethodSelect = document.getElementById('shipping_method');

        // Check if a shipping method is selected
        if (shippingMethodSelect && shippingMethodSelect.selectedIndex >= 0) {
            const selectedOption = shippingMethodSelect.options[shippingMethodSelect.selectedIndex];
            shippingFee = parseFloat(selectedOption.getAttribute('data-fee')) || 0; // Get shipping fee from selected option or default to 0
        } else {
            shippingFee = 0; // If no shipping method is selected, set fee to 0
        }

        // Calculate discount amount (if any)
        const discountAmount = baseTotalPrice * (discountPercentage / 100);

        // Calculate final price after discount
        const finalPrice = baseTotalPrice - discountAmount;

        // Calculate the final total price (after discount and including shipping fee)
        const finalTotalPrice = finalPrice + shippingFee;

        // Update the displayed prices dynamically
        document.getElementById('total-amount').innerText = "Subtotal: $" + baseTotalPrice.toFixed(2);
        document.getElementById('shipping-fee').innerText = "Shipping Fee: $" + shippingFee.toFixed(2);

        // Display coupon discount only if a coupon is applied
        if (discountPercentage > 0) {
            const discountAmount = baseTotalPrice * (discountPercentage / 100);
            document.getElementById('coupon-discount').innerText = "Coupon Applied: -$" + discountAmount.toFixed(2);
        } else {
            document.getElementById('coupon-discount').innerText = ""; // Clear coupon discount if no coupon is applied
        }

        // Update the final total price
        document.getElementById('final-total').innerText = "Total Amount: $" + finalTotalPrice.toFixed(2);
    }

    // Trigger the update when the page loads (to apply the discount and shipping immediately)
    window.onload = updateTotalPrice;

    // Add an event listener to the shipping method dropdown to update the total price when the shipping method changes
    document.getElementById('shipping_method').addEventListener('change', updateTotalPrice);
</script>




    <?php
    $conn->close();
    ?>