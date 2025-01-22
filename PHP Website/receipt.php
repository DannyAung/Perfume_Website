<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if order_id is passed in the URL
if (!isset($_GET['order_id'])) {
    echo "Invalid request: Order ID is missing.";
    exit;
}

$order_id = $_GET['order_id'];

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

// Fetch order details, including coupon and shipping information
$sql = "SELECT o.order_id, o.total_price, o.created_at, o.shipping_method, o.shipping_fee, 
               o.coupon_code, o.discount_percentage, o.coupon_id, u.user_name, u.email, u.phone_number, 
               o.payment_method, s.delivery_time
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        LEFT JOIN shipping s ON o.shipping_method = s.shipping_method
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Check if the order was found
if ($order_result->num_rows == 0) {
    echo "Order not found for Order ID: " . $order_id;
    exit;
}

$order = $order_result->fetch_assoc();

// Fetch order items and calculate total price with product discounts
$order_items_sql = "SELECT oi.product_id, oi.product_name, oi.quantity, oi.size, p.price, p.discounted_price
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.product_id
                    WHERE oi.order_id = ?";
$order_items_stmt = $conn->prepare($order_items_sql);
$order_items_stmt->bind_param("i", $order_id);
$order_items_stmt->execute();
$order_items_result = $order_items_stmt->get_result();

// Check if order items are found
if ($order_items_result->num_rows == 0) {
    echo "No order items found for Order ID: " . $order_id;
    exit;
}

$order_items = [];
$total_price_with_discount = 0;

while ($item = $order_items_result->fetch_assoc()) {
    $regular_price = $item['price'];
    $discounted_price = $item['discounted_price'] > 0 ? $item['discounted_price'] : $regular_price;
    $item_total = $discounted_price * $item['quantity'];
    $total_price_with_discount += $item_total;
    $order_items[] = $item;
}

// Calculate the discount from the coupon (if any)
$discount_amount = ($total_price_with_discount * $order['discount_percentage']) / 100;

// Final total after applying product discount, coupon discount, and shipping fee
$final_total_price = $total_price_with_discount - $discount_amount + $order['shipping_fee'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .receipt-container {        
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 50px;
        }

        .receipt-container h2,
        .receipt-container h4 {
            font-weight: bold;
            color: #007bff;
        }

        .card {
            border: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-primary {
            padding: 10px 30px;
            background-color: #007bff;
            border: none;
            font-size: 1rem;
        }

        .table th,
        .table td {
            text-align: center;
            padding: 10px;
        }

        .table {
            margin-top: 20px;
        }

        .total-summary h3 {
            color: #28a745;
            font-size: 1.5rem;
        }

        .total-summary p {
            font-size: 1.1rem;
        }

        .total-summary .btn {
            margin-top: 20px;
        }

        /* Adjust card titles and text */
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .order-item-price {
            font-weight: bold;
        }

        .coupon-discount {
            color: #dc3545;
        }

        .shipping-fee {
            color: #17a2b8;
        }

    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="receipt-container">
            <h2 class="text-center mb-4">Order Receipt</h2>
            <h5 class="text-center mb-4">Thank You For Your Purchase!</h5>

            <!-- Order Details -->
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Details</h5>
                            <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
                            <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            <p><strong>Shipping Method:</strong> <?php echo $order['shipping_method']; ?></p>
                            <p><strong>Delivery Time:</strong> <?php echo htmlspecialchars($order['delivery_time']); ?></p>
                            <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
                            <p><strong>Shipping Fee:</strong> $<?php echo number_format($order['shipping_fee'], 2); ?></p>
                            <?php if ($order['discount_percentage'] > 0): ?>
                                <p><strong class="coupon-discount">Coupon Discount:</strong> <?php echo number_format($order['discount_percentage'], 2); ?>%</p>
                                <p><strong>Coupon Code:</strong> <?php echo $order['coupon_code']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Customer Details</h5>
                            <p><strong>Name:</strong> <?php echo $order['user_name']; ?></p>
                            <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                            <p><strong>Phone Number:</strong> <?php echo $order['phone_number']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <h4 class="mt-4">Order Items</h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item) {
                        $regular_price = $item['price'];
                        $discounted_price = $item['discounted_price'] > 0 ? $item['discounted_price'] : $regular_price;
                        $item_total = $discounted_price * $item['quantity'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo htmlspecialchars($item['size']); ?></td>
                            <td class="order-item-price">$<?php echo number_format($discounted_price, 2); ?></td>
                            <td class="order-item-price">$<?php echo number_format($item_total, 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Total Summary -->
            <div class="total-summary mt-4">
                <h3><strong>Total Summary</strong></h3>
                <p><strong>Total Price (Before Discount):</strong> $<?php echo number_format($total_price_with_discount, 2); ?></p>
                <?php if ($order['discount_percentage'] > 0): ?>
                    <p><strong>Coupon Discount:</strong> -$<?php echo number_format($discount_amount, 2); ?></p>
                <?php endif; ?>
                <p><strong class="shipping-fee">Shipping Fee:</strong> $<?php echo number_format($order['shipping_fee'], 2); ?></p>
                <h5><strong>Total Amount:</strong> $<?php echo number_format($final_total_price, 2); ?></h5>
            </div>

            <!-- Continue Shopping Button -->
            <div class="btn-container">
                <a href="user_index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>