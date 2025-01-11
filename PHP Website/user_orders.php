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

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

// Fetch orders and their items along with user details
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

// Initialize an array to group items by order_id
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
        'size' => $row['size'],  // Added 'size' to items
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
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <a class="navbar-brand d-flex align-items-center" href="user_index.php">
                <img src="./images/perfume_logo.png" alt="Logo" style="width:50px; height:auto;">
                <b class="ms-2" style="font-family: 'Roboto', sans-serif; font-weight: 300; color: #333;">FRAGRANCE HAVEN</b>
            </a>

            <!-- Toggler for Small Screens -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex flex-column flex-lg-row w-100 align-items-center">

                    <!-- Modern Search Bar in the Center -->
                    <div class="search-bar-container mx-lg-auto my- my-lg-0 w-100 w-lg-auto">
                        <form method="GET" action="search.php" class="search-form d-flex">
                            <input type="text" class="form-control border-end-0 search-input" name="query" placeholder="Search for a product..." aria-label="Search" required>
                            <button class="btn btn-primary search-btn border-start-1 rounded-end-2 px-4  shadow-lg" type="submit">
                                <i class="bi bi-search"></i> <!-- FontAwesome or Bootstrap Icons -->
                            </button>
                        </form>
                    </div>

                    <!-- Display Username or Guest -->
                    <span class="navbar-text mx-lg-3 my-2 my-lg-0 text-center">
                        Welcome, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>!
                    </span>

                    <!-- Account Dropdown for Logged-In Users -->
                    <?php if ($is_logged_in): ?>
                        <div class="dropdown mx-lg-3 my-2 my-lg-0">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Account
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                                <li><a class="dropdown-item" href="user_orders.php">Orders</a></li>
                                <li><a class="dropdown-item" href="user_profile.php">View Profile</a></li>
                                <li><a class="dropdown-item" href="user_logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>


                    <!-- Login and Cart Buttons -->
                    <div class="d-flex justify-content-center justify-content-lg-end my-2 my-lg-0">
                        <?php if (!$is_logged_in): ?>
                            <a href="user_login.php" class="btn login-btn me-3 ">Login/Register</a>
                        <?php endif; ?>
                        <!-- Favorite Link -->
                        <a class="nav-link d-flex align-items-center justify-content-center mx-lg-3 my-2 my-lg-0" href="favorite.php">
                            <i class="bi bi-heart fs-5"></i> <!-- Larger Icon -->
                        </a>
                        <a href="add_to_cart.php" class="btn cart-btn" id="cart-button">
                            <img src="./images/cart-icon.jpg" alt="Cart" style="width:24px; height:24px; margin-right:2px;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

     <!-- Breadcrumb Navigation -->
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

                                <!-- Add review form for completed orders -->
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
</div>
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">

            <div class="col-md-4 mb-4">
                <h5 class="mb-3">About Us</h5>
                <p class="text-muted">Fragrance Haven is your ultimate destination for high-quality perfumes that elevate your senses. Explore our wide range of fragrances designed to suit every occasion and personality.</p>
            </div>

            <div class="col-md-2 mb-4">
                <h5 class="mb-1">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="user_index.php" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="women_category.php" class="text-white text-decoration-none">Women’s Collection</a></li>
                    <li><a href="men_category.php" class="text-white text-decoration-none">Men’s Collection</a></li>
                    <li><a href="unisex_category.php" class="text-white text-decoration-none">Unisex Collection</a></li>
                    <li><a href="about_us.php" class="text-white text-decoration-none">About Us</a></li>
                    <li><a href="contact_us.php" class="text-white text-decoration-none">Contact Us</a></li>
                </ul>
            </div>

            <div class="col-md-2 mb-4">
                <h5 class="mb-1">Customer Care</h5>
                <ul class="list-unstyled">
                    <li><a href="privacy_policy.php" class="text-white text-decoration-none">Privacy Policy</a></li>
                    <li><a href="term_and_conditions.php" class="text-white text-decoration-none">Terms and Conditions</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h5 class="mb-4">Contact Info</h5>
                <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> Pyi Yeik Thar Street, Kamayut, Yangon, Myanmar</p>
                <p class="text-muted"><i class="fas fa-phone-alt me-2"></i> +959450197415</p>
                <p class="text-muted"><i class="fas fa-envelope me-2"></i> support@fragrancehaven.com</p>
            </div>
        </div>

        <div class="row mt-4 border-top pt-3">
            <div class="col-md-6">
                <p class="text-muted">&copy; 2025 Fragrance Haven. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="https://www.instagram.com/" class="text-white me-3 text-decoration-none" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
                <a href="https://www.facebook.com/" class="text-white me-3 text-decoration-none" target="_blank"><i class="fab fa-facebook fa-lg"></i></a>
                <a href="https://twitter.com/" class="text-white text-decoration-none" target="_blank"><i class="fab fa-twitter fa-lg"></i></a>
            </div>
        </div>
    </div>
</footer>

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
