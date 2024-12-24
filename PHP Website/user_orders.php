<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecom_website";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user_id (Assuming user is logged in and their id is stored in session)
session_start();
$user_id = $_SESSION['user_id']; // Replace with your actual session variable

// Fetch orders and their items
$sql = "
    SELECT o.order_id, o.created_at, o.total_price, o.status, 
           oi.order_item_id, oi.product_id, oi.quantity, oi.price, 
           p.product_name, p.image
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.user_id = ?
    ORDER BY o.created_at ASC, oi.order_item_id ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to group items by order_id
$orders = [];

while ($row = $result->fetch_assoc()) {
    $order_id = $row['order_id'];
    if (!isset($orders[$order_id])) {
        $orders[$order_id] = [
            'created_at' => $row['created_at'],
            'total_price' => $row['total_price'],
            'status' => $row['status'],
            'items' => []
        ];
    }
    $orders[$order_id]['items'][] = [
        'product_name' => $row['product_name'],
        'image' => $row['image'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
// Fetch products
$sql = "SELECT product_name, price, image FROM products"; // Ensure table columns are correct
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error in query: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo and Brand -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="./images/Logo.png" alt="Logo" style="width:50px; height:auto;">
            <b class="ms-2 dm-serif-display-regular-italic custom-font-color">FRAGRANCE HAVEN</b>
        </a>

        <!-- Toggler Button for Small Screens -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Content -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex flex-column flex-lg-row w-100 align-items-center">
                <!-- Search Bar in the Center -->
                <div class="mx-auto my-2 my-lg-0">
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>

                <!-- Display Username or Guest -->
                <span class="navbar-text me-3 my-2 my-lg-0">
                    Welcome, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>!
                </span>

                <!-- Account Dropdown for Logged-In Users -->
                <?php if ($is_logged_in): ?>
                    <div class="dropdown me-3">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Account
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                            <li><a class="dropdown-item" href="user_orders.php">Orders</a></li>
                            <li><a class="dropdown-item" href="edit_profile.php">Edit Profile</a></li>
                            <li><a class="dropdown-item" href="user_logout.php">Logout</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Login and Cart Buttons on the Right -->
        <div class="d-flex justify-content-center justify-content-lg-end my-2 my-lg-0">
            <?php if (!$is_logged_in): ?>
                <a href="user_login.php" class="btn login-btn me-3">Login/Register</a>
            <?php endif; ?>
            <a href="add_to_cart.php" class="btn cart-btn" id="cart-button">
            <img src="./images/cart-icon.jpg" alt="Cart" style="width:20px; height:20px; margin-right:6px;">
            Cart 
            </a>
        </div>
    </div>
</nav>

     <!-- New Navigation Links Section -->
     <div class="py-1">
        <div class="container">
            <ul class="nav justify-content">
                <li class="nav-item">
                    <a class="nav-link active" href="user_index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">
                    Category
                </a>
                <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                    <li><a class="dropdown-item" href="#">Men</a></li>
                    <li><a class="dropdown-item" href="#">Women</a></li>
                    <li><a class="dropdown-item" href="#">Unisex</a></li>
                </ul>
            </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Delivery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
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
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                        <p><strong>Total Price:</strong> <?php echo number_format($order['total_price'], 2); ?> MMK</p>
                        <ul class="list-group">
                            <?php foreach ($order['items'] as $item): ?>
                                <li class="list-group-item">
                                    <div class="d-flex">
                                        <img src="products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="me-3" style="width: 50px;">
                                        <div>
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                            Quantity: <?php echo htmlspecialchars($item['quantity']); ?><br>
                                            Price: <?php echo number_format($item['price'], 2); ?> MMK
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no orders yet.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close statement and connection
$stmt->close();
$conn->close();
?>