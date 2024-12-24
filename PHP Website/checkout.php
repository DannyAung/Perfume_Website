<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to check out.";
    exit;
}

$user_id = $_SESSION['user_id']; 

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
$sql = "SELECT ci.cart_item_id, ci.quantity, p.product_id, p.product_name, p.price, p.discounted_price, p.image
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.product_id
        WHERE ci.user_id = ? AND ci.ordered_status = 'not_ordered'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
$cart_items = [];

while ($item = $result->fetch_assoc()) {
    $regular_price = $item['price'];
    $discounted_price = $item['discounted_price'] > 0 ? $item['discounted_price'] : 0;
    $item_price = $discounted_price > 0 ? $discounted_price : $regular_price;
    $item_total = $item_price * $item['quantity'];
    $total_price += $item_total;
    
    $cart_items[] = $item;
}

if (isset($_POST['checkout'])) {
    // Retrieve form data
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $payment_method = $_POST['payment_method'];

    // Additional payment method details
    if ($payment_method == 'kpay') {
        $kpay_phone = $_POST['kpay_phone'];
        $kpay_otp = $_POST['kpay_otp'];
    } elseif ($payment_method == 'credit_card') {
        $card_number = $_POST['card_number'];
        $card_expiry = $_POST['card_expiry'];
        $card_cvv = $_POST['card_cvv'];
    }

    // Insert order into the orders table
    $order_sql = "INSERT INTO orders (user_id, total_price, name, address, phone, email, payment_method) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("idsssss", $user_id, $total_price, $name, $address, $phone, $email, $payment_method);

    if ($order_stmt->execute()) {
        $order_id = $conn->insert_id; // Get the generated order ID

        // Insert each cart item into the order_items table
        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $order_item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) 
                               VALUES (?, ?, ?, ?, ?)";
            $order_item_stmt = $conn->prepare($order_item_sql);
            $order_item_stmt->bind_param("iisid", $order_id, $product_id, $item['product_name'], $quantity, $price);
            $order_item_stmt->execute();
        }

        // Update cart items with the generated order ID
        foreach ($cart_items as $item) {
            $update_sql = "UPDATE cart_items SET ordered_status = 'ordered', order_id = ? WHERE cart_item_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ii", $order_id, $item['cart_item_id']);
            $update_stmt->execute();
        }

        // Redirect to order confirmation page
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
    } else {
        die("Error executing order query: " . $order_stmt->error);
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

    <script>
        // JavaScript to show/hide payment method fields based on selection
        function togglePaymentFields() {
            var paymentMethod = document.getElementById("payment_method").value;
            
            // Hide all payment method fields initially
            document.getElementById("kpay_fields").style.display = "none";
            document.getElementById("credit_card_fields").style.display = "none";
            document.getElementById("cash_on_delivery_fields").style.display = "none";
            
            // Show the relevant fields based on the selected payment method
            if (paymentMethod == "kpay") {
                document.getElementById("kpay_fields").style.display = "block";
            } else if (paymentMethod == "credit_card") {
                document.getElementById("credit_card_fields").style.display = "block";
            } else if (paymentMethod == "cash_on_delivery") {
                document.getElementById("cash_on_delivery_fields").style.display = "block";
            }
        }
    </script>
</head>
<body>

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
                    echo "<p>Quantity: " . $item['quantity'] . "</p>
                          </div>
                          </div>";
                }
            }
            ?>

            <div class="total-price">
                <p>Total Price: $<?php echo number_format($total_price, 2); ?></p>
            </div>
        </div>

        <!-- Checkout Form -->
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
                <select class="form-control" id="payment_method" name="payment_method" required onchange="togglePaymentFields()">
                    <option value="" selected disabled>Select Payment Method</option>
                    <option value="kpay">K Pay</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                </select>
            </div>
 
            
           <div id="kpay_fields" style="display: none;">
    <div class="mb-3">
        <label for="kpay_phone" class="form-label">Phone Number</label>
        <input 
            type="text" 
            class="form-control" 
            id="kpay_phone" 
            name="kpay_phone" 
            placeholder="Enter your K Pay phone number"
            maxlength="11"
        >
    </div>
    <div class="mb-3">
        <label for="kpay_otp" class="form-label">OTP</label>
        <input 
            type="text" 
            class="form-control" 
            id="kpay_otp" 
            name="kpay_otp" 
            placeholder="Enter OTP"
        >
    </div>
    <!-- Send OTP Button -->
    <div class="mb-3" id="send_otp_container" style="display: none;">
        <button type="button" class="btn btn-primary" id="send_otp_btn">Send OTP</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('kpay_phone');
        const sendOtpContainer = document.getElementById('send_otp_container');

        phoneInput.addEventListener('input', function () {
            const phoneNumber = phoneInput.value.trim();

            // Show the Send OTP button if phone number has 11 digits
            if (phoneNumber.length === 11) {
                sendOtpContainer.style.display = 'block';
            } else {
                sendOtpContainer.style.display = 'none';
            }
        });
    });
</script>


            <div id="credit_card_fields" style="display: none;">
                <div class="mb-3">
                    <label for="card_number" class="form-label">Card Number</label>
                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9876 5432">
                </div>
                <div class="mb-3">
                    <label for="card_expiry" class="form-label">Expiry Date</label>
                    <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="MM/YY">
                </div>
                <div class="mb-3">
                    <label for="card_cvv" class="form-label">CVV</label>
                    <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="123">
                </div>
            </div>

            <div id="cash_on_delivery_fields" style="display: none;">
                <div class="mb-3">
                    <p>No additional details requires. Please confirm your order.</p>
                </div>
            </div> 

            <button type="submit" name="checkout" class="btn btn-success btn-lg w-100">Confirm and Checkout</button>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
