<?php
// Start session
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3307;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    echo "Please log in to add items to the cart.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1; // Default quantity is 1 if not provided

    // Fetch product details from the POST data
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];

    // Check if the product is already in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        // If it's already in the cart, increase the quantity
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        // Otherwise, add the product with the quantity to the cart
        $_SESSION['cart'][$product_id] = [
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_image' => $product_image,
            'quantity' => $quantity,
        ];
    }

    // Insert or update cart items in the database
    $user_id = $_SESSION['user_id'];  // Assuming user_id is set when logged in
    $query = "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Product already exists in the cart, update quantity
        $update_query = "UPDATE cart_items SET quantity = quantity + ?, price = price * ? WHERE user_id = ? AND product_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('iiii', $quantity, $product_price, $user_id, $product_id);
        $update_stmt->execute();
    } else {
        // Product does not exist, insert a new entry
        $insert_query = "INSERT INTO cart_items (user_id, product_id, quantity, added_at, ordered_status, price) 
                         VALUES (?, ?, ?, NOW(), 'in_cart', ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param('iiid', $user_id, $product_id, $quantity, $product_price);
        $insert_stmt->execute();
    }

    // Redirect to the cart page after adding the item
    header('Location: add_to_cart.php');
    exit();
}

// Handle removal of cart item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $remove_product_id = $_POST['product_id'];

    // Remove from the session cart
    unset($_SESSION['cart'][$remove_product_id]);

    // Also remove from the database
    $user_id = $_SESSION['user_id'];
    $remove_query = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
    $remove_stmt = $conn->prepare($remove_query);
    $remove_stmt->bind_param('ii', $user_id, $remove_product_id);
    $remove_stmt->execute();

    // Redirect to the cart page after removal
    header('Location: add_to_cart.php');
    exit();
}

// Get cart items from the session
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <title>Your Cart</title>
</head>
<body>
<div class="container mt-5">
    <h2>Your Cart</h2>

    <?php if (!empty($cart_items)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $grand_total = 0; ?>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td>
                            <img src="uploads/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                 style="width: 50px; height: 50px;">
                        </td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></td>
                        <td>
                            <form action="add_to_cart.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <input type="hidden" name="remove_item" value="1">
                                <button type="submit" class="btn btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                    <?php $grand_total += $item['product_price'] * $item['quantity']; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                    <td><strong>$<?php echo number_format($grand_total, 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <a href="user_index.php" class="btn btn-primary">Continue Shopping</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
