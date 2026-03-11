<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$is_logged_in = true;

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['checkout_started'])) {
    unset($_SESSION['applied_coupon_code']);
    unset($_SESSION['coupon_data']);
    unset($_SESSION['discount_percentage']);
    unset($_SESSION['coupon_id']);
    unset($_SESSION['coupon_yes']);
    unset($_SESSION['checkout_started']);
}

$cart_items = [];
$total_price = 0;
$shipping_methods = [];
$shipping_fee = 0;
$discount_percentage = $_SESSION['discount_percentage'] ?? 0;
$discount_amount = 0;
$final_total_price = 0;
$applied_coupon_code = $_SESSION['applied_coupon_code'] ?? "";
$coupon_id = $_SESSION['coupon_id'] ?? null;
$coupon_yes = $_SESSION['coupon_yes'] ?? false;

try {
    // Get cart items
    $sql = "
        SELECT 
            ci.product_id,
            ci.quantity,
            p.product_name,
            p.price,
            p.discounted_price,
            p.image,
            p.size,
            p.subcategory
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.user_id = :user_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $regular_price = (float)$item['price'];
        $discounted_price = (
            isset($item['subcategory'], $item['discounted_price']) &&
            $item['subcategory'] === 'discount' &&
            (float)$item['discounted_price'] > 0 &&
            (float)$item['discounted_price'] < $regular_price
        ) ? (float)$item['discounted_price'] : 0;

        $item_price = $discounted_price > 0 ? $discounted_price : $regular_price;
        $item_total = $item_price * (int)$item['quantity'];
        $total_price += $item_total;
    }

    // Shipping methods
    $shipping_sql = "SELECT shipping_method, shipping_fee FROM shipping";
    $shipping_stmt = $pdo->query($shipping_sql);
    $shipping_rows = $shipping_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($shipping_rows as $row) {
        $shipping_methods[$row['shipping_method']] = (float)$row['shipping_fee'];
    }

    if (isset($_POST['shipping_method']) && !empty($_POST['shipping_method'])) {
        $shipping_method = $_POST['shipping_method'];
        $shipping_fee = $shipping_methods[$shipping_method] ?? 0;
    }

    $discount_amount = $total_price * ($discount_percentage / 100);
    $final_total_price = $total_price - $discount_amount + $shipping_fee;

    // Apply coupon
    if (isset($_POST['apply_coupon'])) {
        $manual_coupon_code = trim($_POST['coupon_code'] ?? '');

        $coupon_sql = "
            SELECT coupon_id, discount_percentage, minimum_purchase_amount, valid_from, valid_to
            FROM coupons
            WHERE coupon_code = :coupon_code
              AND valid_from <= NOW()
              AND valid_to >= NOW()
        ";
        $coupon_stmt = $pdo->prepare($coupon_sql);
        $coupon_stmt->execute([':coupon_code' => $manual_coupon_code]);
        $coupon_data = $coupon_stmt->fetch(PDO::FETCH_ASSOC);

        if ($coupon_data) {
            if ($total_price >= (float)$coupon_data['minimum_purchase_amount']) {
                $_SESSION['coupon_yes'] = true;
                $_SESSION['discount_percentage'] = (float)$coupon_data['discount_percentage'];
                $_SESSION['applied_coupon_code'] = $manual_coupon_code;
                $_SESSION['coupon_data'] = $manual_coupon_code;
                $_SESSION['coupon_id'] = $coupon_data['coupon_id'];

                $discount_percentage = (float)$coupon_data['discount_percentage'];
                $discount_amount = ($discount_percentage / 100) * $total_price;
                $final_total_price = $total_price - $discount_amount + $shipping_fee;

                echo "<script>alert('Coupon applied successfully!');</script>";
            } else {
                echo "<script>alert('This coupon requires a minimum purchase of $" . $coupon_data['minimum_purchase_amount'] . ".');</script>";
            }
        } else {
            echo "<script>alert('Invalid or expired coupon code!');</script>";
        }

        $applied_coupon_code = $_SESSION['applied_coupon_code'] ?? "";
        $coupon_id = $_SESSION['coupon_id'] ?? null;
        $coupon_yes = $_SESSION['coupon_yes'] ?? false;
    }

    // Checkout
    if (isset($_POST['checkout'])) {
        if (empty($cart_items)) {
            die("Your cart is empty.");
        }

        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $payment_method = trim($_POST['payment_method'] ?? '');
        $shipping_method = trim($_POST['shipping_method'] ?? '');
        $shipping_fee = $shipping_methods[$shipping_method] ?? 0;

        $discount_percentage = $_SESSION['discount_percentage'] ?? 0;
        $discount_amount = ($discount_percentage / 100) * $total_price;
        $final_total_price = $total_price - $discount_amount + $shipping_fee;

        $coupon_code = $_SESSION['coupon_data'] ?? null;
        $coupon_id = $_SESSION['coupon_id'] ?? null;
        $coupon_yes = $_SESSION['coupon_yes'] ?? false;

        $pdo->beginTransaction();

        $order_sql = "
            INSERT INTO orders
            (user_id, total_price, name, address, phone, email, payment_method, shipping_method, shipping_fee, discount_percentage, coupon_code, coupon_id)
            VALUES
            (:user_id, :total_price, :name, :address, :phone, :email, :payment_method, :shipping_method, :shipping_fee, :discount_percentage, :coupon_code, :coupon_id)
        ";
        $order_stmt = $pdo->prepare($order_sql);
        $order_stmt->execute([
            ':user_id' => $user_id,
            ':total_price' => $final_total_price,
            ':name' => $name,
            ':address' => $address,
            ':phone' => $phone,
            ':email' => $email,
            ':payment_method' => $payment_method,
            ':shipping_method' => $shipping_method,
            ':shipping_fee' => $shipping_fee,
            ':discount_percentage' => $discount_percentage,
            ':coupon_code' => $coupon_yes ? $coupon_code : null,
            ':coupon_id' => $coupon_yes ? $coupon_id : null
        ]);

        $order_id = $pdo->lastInsertId();

        foreach ($cart_items as $item) {
            $product_id = (int)$item['product_id'];
            $quantity = (int)$item['quantity'];

            $price = (
                isset($item['subcategory'], $item['discounted_price']) &&
                $item['subcategory'] === 'discount' &&
                (float)$item['discounted_price'] > 0 &&
                (float)$item['discounted_price'] < (float)$item['price']
            ) ? (float)$item['discounted_price'] : (float)$item['price'];

            $order_item_sql = "
                INSERT INTO order_items (order_id, product_id, product_name, quantity, price, size)
                VALUES (:order_id, :product_id, :product_name, :quantity, :price, :size)
            ";
            $order_item_stmt = $pdo->prepare($order_item_sql);
            $order_item_stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':product_name' => $item['product_name'],
                ':quantity' => $quantity,
                ':price' => $price,
                ':size' => $item['size']
            ]);

            $update_stock_sql = "
                UPDATE products
                SET stock_quantity = stock_quantity - :quantity
                WHERE product_id = :product_id
            ";
            $update_stock_stmt = $pdo->prepare($update_stock_sql);
            $update_stock_stmt->execute([
                ':quantity' => $quantity,
                ':product_id' => $product_id
            ]);
        }

        // Clear cart after order
        $clear_cart_sql = "DELETE FROM cart_items WHERE user_id = :user_id";
        $clear_cart_stmt = $pdo->prepare($clear_cart_sql);
        $clear_cart_stmt->execute([':user_id' => $user_id]);

        $pdo->commit();

        unset($_SESSION['discount_percentage']);
        unset($_SESSION['applied_coupon_code']);
        unset($_SESSION['coupon_id']);
        unset($_SESSION['coupon_data']);
        unset($_SESSION['coupon_yes']);

        header("Location: receipt.php?order_id=" . $order_id);
        exit;
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Database error: " . $e->getMessage());
}

$applied_coupon_code_html = htmlspecialchars($applied_coupon_code, ENT_QUOTES, 'UTF-8');

// Payment methods
$payment_stmt = $pdo->query("SELECT payment_method FROM payment ORDER BY created_at ASC");
$payment_methods = $payment_stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="add_to_cart.php">Your Cart</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </div>
    </nav>

    <div class="checkout-container">
        <h1>Checkout</h1>

        <div class="cart-item-summary">
            <h3>Cart Summary</h3>
            <?php if (!empty($cart_items)): ?>
                <?php foreach ($cart_items as $item): ?>
                    <?php
                    $regular_price = (float)$item['price'];
                    $discounted_price = (
                        isset($item['subcategory'], $item['discounted_price']) &&
                        $item['subcategory'] === 'discount' &&
                        (float)$item['discounted_price'] > 0 &&
                        (float)$item['discounted_price'] < $regular_price
                    ) ? (float)$item['discounted_price'] : 0;

                    $item_price = $discounted_price > 0 ? $discounted_price : $regular_price;
                    $image_path = "products/" . htmlspecialchars($item['image']);
                    ?>
                    <div class='cart-item'>
                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-image" width="100">
                        <div class='product-details'>
                            <p><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></p>
                            <?php if ($discounted_price > 0): ?>
                                <p class='text-muted'><del>$<?php echo number_format($regular_price, 2); ?></del></p>
                                <p><strong>$<?php echo number_format($discounted_price, 2); ?></strong></p>
                            <?php else: ?>
                                <p><strong>$<?php echo number_format($regular_price, 2); ?></strong></p>
                            <?php endif; ?>
                            <p>Quantity: <?php echo (int)$item['quantity']; ?> <span class='text-muted'>Size: <?php echo htmlspecialchars($item['size']); ?></span></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <form method="POST" action="checkout.php">
            <label for="coupon_code">Enter Coupon Code:</label>
            <input type="text" name="coupon_code" id="coupon_code" class="form-control" value="<?php echo $applied_coupon_code_html; ?>">
            <button type="submit" name="apply_coupon" class="btn btn-primary mt-2">Apply Coupon</button>
        </form>

        <form method="post" class="checkout-form mt-4">
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

            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required onchange="handlePaymentMethodChange()">
                    <option value="" selected disabled>Select Payment Method</option>
                    <?php if (!empty($payment_methods)): ?>
                        <?php foreach ($payment_methods as $row): ?>
                            <option value="<?php echo htmlspecialchars($row['payment_method']); ?>">
                                <?php echo htmlspecialchars($row['payment_method']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No payment methods available</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-3" id="phone_number_div" style="display:none;">
                <label for="phone_number" class="form-label">Phone Number (11 digits)</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" maxlength="11" pattern="\d{11}">
            </div>

            <div class="mb-3" id="otp_div" style="display:none;">
                <label for="otp" class="form-label">OTP (6 digits)</label>
                <input type="text" class="form-control" id="otp" name="otp" maxlength="6" pattern="\d{6}">
            </div>

            <div class="mb-3" id="credit_card_div" style="display:none;">
                <label for="card_number" class="form-label">Card Number</label>
                <input type="text" class="form-control" id="card_number" name="card_number" maxlength="19" pattern="\d{16,19}" placeholder="Enter your card number">

                <label for="exp_date" class="form-label mt-2">Expiration Date (MM/YY)</label>
                <input type="text" class="form-control" id="exp_date" name="exp_date" maxlength="5" pattern="\d{2}/\d{2}" placeholder="MM/YY">

                <label for="cvv" class="form-label mt-2">CVV</label>
                <input type="text" class="form-control" id="cvv" name="cvv" maxlength="3" pattern="\d{3}" placeholder="Enter CVV">
            </div>

            <div class="mb-3">
                <label for="shipping_method" class="form-label">Shipping Method</label>
                <select class="form-control" id="shipping_method" name="shipping_method" onchange="updateTotalPrice()" required>
                    <option value="" disabled selected>Select Shipping Method</option>
                    <?php foreach ($shipping_methods as $method => $fee): ?>
                        <option value="<?php echo htmlspecialchars($method); ?>" data-fee="<?php echo $fee; ?>">
                            <?php echo htmlspecialchars($method); ?> - $<?php echo number_format($fee, 2); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="total-price">
                <p id="total-amount">Subtotal: $<?php echo number_format($total_price, 2); ?></p>

                <?php if ($discount_percentage > 0): ?>
                    <p id="coupon-discount">Coupon Applied: -$<?php echo number_format($discount_amount, 2); ?></p>
                <?php else: ?>
                    <p id="coupon-discount"></p>
                <?php endif; ?>

                <p id="shipping-fee">Shipping Fee: $<?php echo number_format($shipping_fee, 2); ?></p>
                <hr>
                <p id="final-total"><strong>Total Amount: $<?php echo number_format($final_total_price, 2); ?></strong></p>
            </div>

            <button type="submit" name="checkout" class="btn btn-success btn-lg w-100">Confirm and Checkout</button>
        </form>
    </div>

    <script>
        const baseTotalPrice = <?php echo (float)$total_price; ?>;
        const discountPercentage = <?php echo (float)$discount_percentage; ?>;
        let shippingFee = <?php echo (float)$shipping_fee; ?>;

        function handlePaymentMethodChange() {
            var paymentMethod = document.getElementById('payment_method').value;

            if (paymentMethod === 'KPay') {
                document.getElementById('phone_number_div').style.display = 'block';
                document.getElementById('otp_div').style.display = 'block';
                document.getElementById('credit_card_div').style.display = 'none';
            } else if (paymentMethod === 'Credit Card') {
                document.getElementById('phone_number_div').style.display = 'none';
                document.getElementById('otp_div').style.display = 'none';
                document.getElementById('credit_card_div').style.display = 'block';
            } else {
                document.getElementById('phone_number_div').style.display = 'none';
                document.getElementById('otp_div').style.display = 'none';
                document.getElementById('credit_card_div').style.display = 'none';
            }
        }

        function updateTotalPrice() {
            const shippingMethodSelect = document.getElementById('shipping_method');

            if (shippingMethodSelect && shippingMethodSelect.selectedIndex >= 0) {
                const selectedOption = shippingMethodSelect.options[shippingMethodSelect.selectedIndex];
                shippingFee = parseFloat(selectedOption.getAttribute('data-fee')) || 0;
            }

            const discountAmount = baseTotalPrice * (discountPercentage / 100);
            const finalPriceAfterDiscount = baseTotalPrice - discountAmount;
            const finalTotalPrice = finalPriceAfterDiscount + shippingFee;

            document.getElementById('total-amount').innerText = "Subtotal: $" + baseTotalPrice.toFixed(2);
            document.getElementById('shipping-fee').innerText = "Shipping Fee: $" + shippingFee.toFixed(2);

            if (discountPercentage > 0) {
                document.getElementById('coupon-discount').innerText = "Coupon Applied: -$" + discountAmount.toFixed(2);
            } else {
                document.getElementById('coupon-discount').innerText = "";
            }

            document.getElementById('final-total').innerText = "Total Amount: $" + finalTotalPrice.toFixed(2);
        }

        window.onload = updateTotalPrice;
        document.getElementById('shipping_method').addEventListener('change', updateTotalPrice);
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>