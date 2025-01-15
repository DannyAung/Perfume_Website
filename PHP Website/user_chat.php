<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit;
}

// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$db_name = 'ecom_website';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4;port=$port", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

// Get user ID
$user_id = $_SESSION['user_id'];

// Fetch chat messages for the user
$stmt = $pdo->prepare("SELECT * FROM chats WHERE user_id = :user_id ORDER BY sent_at ASC");
$stmt->execute([':user_id' => $user_id]);
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Admin</title>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <style>
        .chat-container {
            max-width: 600px;
            margin: 20px auto;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
        }

        .chat-bubble {
            margin-bottom: 10px;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 75%;
            word-wrap: break-word;
        }

        .user-bubble {
            background-color: #007bff;
            color: white;
            text-align: left;
            margin-left: auto;
        }

        .admin-bubble {
            background-color: #e9ecef;
            text-align: left;
            margin-right: auto;
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


    <div class="container py-5">
    <div class="chat-container p-9 rounded-3 shadow-sm" style="background: #f8f9fa;">
        <!-- Chat GIF Section -->
        <div class="text-center mb-1">
            <img src="images/chat.gif" alt="Chat GIF" class="img-fluid" style="max-width: 180px;">
        </div>

        <!-- Chat Heading -->
        <h3 class="text-center mb-3" style="font-family: 'Roboto', sans-serif; font-weight: 400; color: #333;">Let's Talk!!</h3>
        <div class="chat-box mb-2" style="height: 250px; overflow-y: scroll; padding-right: 15px;">
            <?php foreach ($chats as $chat): ?>
                <div class="chat-bubble <?php echo $chat['sender'] === 'user' ? 'user-bubble' : 'admin-bubble'; ?> mb-3">
                    <small class="fw-bold"><?php echo $chat['sender'] === 'user' ? 'You' : 'Admin'; ?></small><br>
                    <p class="m-0"><?php echo htmlspecialchars($chat['message']); ?></p>
                    <small class="text-muted"><?php echo $chat['sent_at']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Input Section -->
        <form action="user_send_message.php" method="POST">
            <div class="input-group">
                <input type="text" name="message" class="form-control rounded-pill border-0 shadow-sm" placeholder="Type your message..." required>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Send</button>
            </div>
        </form>
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


</body>

</html>