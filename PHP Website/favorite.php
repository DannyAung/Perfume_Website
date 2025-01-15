<?php
// Start session and include database connection
session_start();
require_once 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle "Add to Wishlist" and "Remove from Wishlist" actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);

    // Check if the product is in the wishlist
    $query = "SELECT * FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    if (isset($_POST['add_to_wishlist'])) {
        // Add to Wishlist
        $query = "SELECT 1 FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            // Item not in wishlist, add it
            $insert_query = "INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $insert_stmt->execute();
            echo "Item added to wishlist.";
        } else {
            echo "Item already in wishlist.";
        }
    } elseif (isset($_POST['remove_from_wishlist'])) {
        // Remove from Wishlist
        $delete_query = "DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $delete_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $delete_stmt->execute();
        echo "Item removed from wishlist.";
    }


    // Redirect back to the previous page (e.g., product_detail.php or user_index.php)
    $referer = isset($_SERVER['HTTP_REFERER']) ? filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_URL) : 'user_index.php';
    header("Location: " . $referer);
    exit;
}

// Fetch wishlist items for the logged-in user
$query = "
    SELECT 
        w.wishlist_id, 
        w.date_added, 
        p.product_id, 
        p.product_name, 
        p.image, 
        p.price, 
        p.discounted_price, 
        p.stock_quantity
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    WHERE w.user_id = :user_id
";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist</title>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .wishlist-table {
            width: 100%;
            border-collapse: collapse;
        }

        .wishlist-table th,
        .wishlist-table td {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
        }

        .wishlist-table img {
            width: 70px;
            height: auto;
            border-radius: 5px;
        }

        .btn-clear,
        .btn-add-all {
            text-decoration: none;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .btn-clear {
            background-color: #dc3545;
        }

        .btn-add-all {
            background-color:rgb(22, 81, 208);
        }
        .cart-container {
            width: 80%;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
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
                <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
            </ol>
        </div>
       
        <div class="wishlist-container container my-5">
    <!-- Wishlist Banner -->
    <div class="wishlist-banner text-center mb-3">
        <h1 class="mt-4">Your Wishlist</h1>
        <p class="text-muted">Your favorite items are just a click away!</p>
    </div>

    <!-- Wishlist Table -->
    <div class="card shadow-lg">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">Remove</th>
                        <th>Product</th>
                        <th>Original Price</th>
                        <th>Discounted Price</th>
                        <th>Date Added</th>
                        <th>Stock Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($wishlist_items)) : ?>
                        <?php foreach ($wishlist_items as $item) : ?>
                            <tr>
                                <!-- Remove item -->
                                <td class="text-center">
                                    <form method="POST" action="remove_from_wishlist.php">
                                        <input type="hidden" name="wishlist_id" value="<?php echo $item['wishlist_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>

                                <!-- Product -->
                                <td class="d-flex align-items-center">
                                    <img src="products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="img-thumbnail me-3" style="width: 70px; height: 70px; object-fit: cover;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                    </div>
                                </td>

                                <!-- Price -->
                                <td>$<?php echo number_format($item['price'], 2); ?></td>

                                <!-- Discounted Price -->
                                <td>
                                    <?php if (!empty($item['discounted_price'])) : ?>
                                        <span class="text-success">$<?php echo number_format($item['discounted_price'], 2); ?></span>
                                    <?php else : ?>
                                        <span class="text-muted">No discount</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Date Added -->
                                <td><?php echo date("d F Y", strtotime($item['date_added'])); ?></td>

                                <!-- Stock Status -->
                                <td>
                                    <?php if ($item['stock_quantity'] > 0) : ?>
                                        <span class="badge bg-success">In stock</span>
                                    <?php else : ?>
                                        <span class="badge bg-danger">Out of stock</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Add to Cart -->
                                <td class="text-center">
                                    <form method="POST" action="add_to_cart1.php">
                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Your wishlist is empty.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
   
        <!-- Clear Wishlist and Add All to Cart Buttons -->
        <div class="d-flex justify-content-between mt-3">
            <form method="POST" action="clear_wishlist.php">
                <button type="submit" class="btn-clear">Clear All</button>
            </form>
            <form method="POST" action="add_all_to_cart.php">
                <button type="submit" class="btn-add-all">Add All to Cart</button>
            </form>
        </div>
    </div>
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
                        <li><a href="faq.php" class="text-white text-decoration-none">FAQ</a></li>
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