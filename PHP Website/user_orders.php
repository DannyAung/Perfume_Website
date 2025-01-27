<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecom_website";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


session_start();
$user_id = $_SESSION['user_id'];

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];


$sql = "
    SELECT o.order_id, o.created_at, o.total_price, o.status, 
           oi.order_item_id, oi.product_id, oi.quantity, oi.price,
           p.product_name, p.image, p.size, 
           u.address, u.phone_number
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    JOIN users u ON o.user_id = u.user_id
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC, oi.order_item_id ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


$orders = [];
while ($row = $result->fetch_assoc()) {
    $order_id = $row['order_id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'created_at' => $row['created_at'],
            'total_price' => $row['total_price'],
            'status' => $row['status'],
            'address' => $row['address'],
            'phone_number' => $row['phone_number'],
            'items' => []
        ];
    }
    $orders[$order_id]['items'][] = [
        'product_name' => $row['product_name'],
        'image' => $row['image'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'size' => $row['size'],
        'product_id' => $row['product_id']
    ];
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
</head>

<body>
    <?php include 'navbar.php'; ?>

    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Orders</li>
            </ol>
        </div>

        <!-- Orders Table -->
        <div class="container mt-4">
            <h2>Your Orders</h2>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order_id => $order): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?>
                        </div>
                        <div class="card-body">
                            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                            <p><strong>Status:</strong>
                                <span class="<?php echo $order['status'] == 'cancelled' ? 'cancelled-status' : ''; ?>">
                                    <?php echo htmlspecialchars($order['status']); ?>
                                </span>
                            </p>
                            <p><strong>Total Price:</strong> <?php echo number_format($order['total_price'], 2); ?> $</p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($order['phone_number']); ?></p>

                            <ul class="list-group">
                                <?php foreach ($order['items'] as $item): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex">
                                            <img src="products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="me-3" style="width: 50px;">
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                                Quantity: <?php echo htmlspecialchars($item['quantity']); ?><br>
                                                Size: <?php echo htmlspecialchars($item['size']); ?><br>
                                            </div>
                                        </div>


                                        <?php if ($order['status'] == 'completed'): ?>
                                            <form action="submit_review.php" method="POST" class="mt-3">
                                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                <div class="mb-2">
                                                    <textarea name="review_text" class="form-control" placeholder="Write your review..." required></textarea>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="rating">Rating:</label>
                                                    <select name="rating" class="form-select" required>
                                                        <option value="">Select Rating</option>
                                                        <option value="1">1 - Poor</option>
                                                        <option value="2">2 - Fair</option>
                                                        <option value="3">3 - Good</option>
                                                        <option value="4">4 - Very Good</option>
                                                        <option value="5">5 - Excellent</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Submit Review</button>
                                            </form>

                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no orders yet.</p>
            <?php endif; ?>
        </div><br><br><br><br><br>
        <?php include 'footer.php'; ?>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>


</body>

</html>

<?php
// Close statement and connection
$stmt->close();
$conn->close();
?>