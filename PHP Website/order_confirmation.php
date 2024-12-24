<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the order ID is provided
if (!isset($_GET['order_id'])) {
    echo "No order ID provided.";
    exit;
}

$order_id = intval($_GET['order_id']);

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

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE order_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "Order not found.";
    exit;
}

$order = $order_result->fetch_assoc();

// Fetch cart items associated with this order
$cart_items_sql = "SELECT ci.quantity, p.product_name, p.price, p.discounted_price 
                   FROM cart_items ci 
                   JOIN products p ON ci.product_id = p.product_id 
                   WHERE ci.order_id = ?";
$cart_items_stmt = $conn->prepare($cart_items_sql);
$cart_items_stmt->bind_param("i", $order_id);
$cart_items_stmt->execute();
$cart_items_result = $cart_items_stmt->get_result();

$cart_items = [];
while ($item = $cart_items_result->fetch_assoc()) {
    $cart_items[] = $item;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
        }
        .confirmation-container {
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
        .order-details {
            margin-top: 20px;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details th, .order-details td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        .order-details th {
            background-color: #f4f4f4;
        }
        .order-summary {
            margin-top: 20px;
            text-align: right;
        }
        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .btn-container button {
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="confirmation-container">
    <h1>Order Confirmation</h1>
    <p>Thank you for your purchase! Below are your order details.</p>
    <p class="delivery-message" style="color: #555; font-style: italic; text-align: center; margin-top: 10px;">
        Your order will be processed and delivered within 2-3 business days. Thank you for shopping with us!
    </p>


        <!-- Order Details -->
        <div class="order-details">
            <h3>Order Information</h3>
            <table>
                <tr>
                    <th>Order ID</th>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td><?php echo htmlspecialchars($order['address']); ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?php echo htmlspecialchars($order['phone']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                </tr>
                <tr>
                    <th>Total Price</th>
                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                </tr>
            </table>
        </div>

        <!-- Cart Items -->
        <div class="order-details">
            <h3>Cart Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): 
                        $regular_price = $item['price'];
                        $discounted_price = $item['discounted_price'] > 0 ? $item['discounted_price'] : $regular_price;
                        $item_price = $discounted_price;
                        $item_subtotal = $item_price * $item['quantity'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>$<?php echo number_format($item_price, 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item_subtotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <h4>Total Price: $<?php echo number_format($order['total_price'], 2); ?></h4>
        </div>

        <div class="btn-container">
    <a href="user_index.php" class="btn btn-primary btn-lg">Continue Shopping</a>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
