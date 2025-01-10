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
echo "Order ID: " . $order_id; // Debugging output to check order_id

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

// Fetch order details and coupon information
$sql = "SELECT o.order_id, o.total_price, o.created_at, o.shipping_method, o.shipping_fee, 
                o.coupon_code, u.user_name, u.email, u.phone_number, o.payment_method
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

// Check if the order was found
if ($order_result->num_rows == 0) {
    echo "Order not found for Order ID: " . $order_id; // Debugging message
    exit;
}

$order = $order_result->fetch_assoc();

// Fetch the discount_percentage from the coupons table using coupon_code
$discount_percentage = 0;
if (!empty($order['coupon_code'])) {
    $coupon_sql = "SELECT discount_percentage FROM coupons WHERE coupon_code = ?";
    $coupon_stmt = $conn->prepare($coupon_sql);
    $coupon_stmt->bind_param("s", $order['coupon_code']);
    $coupon_stmt->execute();
    $coupon_result = $coupon_stmt->get_result();
    if ($coupon_result->num_rows > 0) {
        $coupon = $coupon_result->fetch_assoc();
        $discount_percentage = $coupon['discount_percentage'];
    }
}

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
    echo "No order items found for Order ID: " . $order_id; // Debugging message
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
$discount_amount = ($total_price_with_discount * $discount_percentage) / 100;

// Final total after applying product discount, coupon discount, and shipping fee
$final_total_price = $total_price_with_discount - $discount_amount + $order['shipping_fee'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Order Receipt</h1>
        <div class="row">
            <div class="col-md-6">
                <h4>Order Details</h4>
                <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                <p><strong>Shipping Method:</strong> <?php echo $order['shipping_method']; ?></p>
                <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
                <p><strong>Shipping Fee:</strong> $<?php echo number_format($order['shipping_fee'], 2); ?></p>
                <?php if ($discount_percentage > 0): ?>
                    <p><strong>Coupon Discount:</strong> <?php echo number_format($discount_percentage, 2); ?>%</p>
                <?php endif; ?>
                <?php if (!empty($order['coupon_code'])): ?>
                    <p><strong>Coupon Code:</strong> <?php echo htmlspecialchars($order['coupon_code']); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h4>Customer Details</h4>
                <p><strong>Name:</strong> <?php echo $order['user_name']; ?></p>
                <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                <p><strong>Phone Number:</strong> <?php echo $order['phone_number']; ?></p>
            </div>
        </div>

        <h4 class="mt-5">Order Items</h4>
        <table class="table">
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
                        <td>$<?php echo number_format($discounted_price, 2); ?></td>
                        <td>$<?php echo number_format($item_total, 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>

        <div class="text-right">
            <h4>Total Price (Before Discount): $<?php echo number_format($total_price_with_discount, 2); ?></h4>
            <?php if ($discount_percentage > 0): ?>
                <h4>Coupon Discount: -$<?php echo number_format($discount_amount, 2); ?></h4>
            <?php endif; ?>
            <h4>Shipping Fee: $<?php echo number_format($order['shipping_fee'], 2); ?></h4>
            <h4><strong>Total Amount (After Discount): $<?php echo number_format($final_total_price, 2); ?></strong></h4>
        </div>
    </div>
</div>
<div class="btn-container">
        <a href="user_index.php" class="btn btn-primary btn-lg">Continue Shopping</a>
    </div>
</body>

</html>

<?php
$conn->close();
?>