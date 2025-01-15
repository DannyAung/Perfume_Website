<?php
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Choosing the Right Perfume | Your Perfume Website</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 36px;
            font-weight: 600;
            color: #333;
        }

        .content {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .content p {
            font-size: 18px;
            line-height: 1.6;
            color: #555;
        }

        .content ol {
            font-size: 18px;
            line-height: 1.6;
            color: #555;
        }

        .tips-list {
            list-style-type: none;
            padding-left: 0;
        }

        .tips-list li {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }

        .tips-list li::before {
            content: '\2022';
            color: #e74c3c;
            font-size: 24px;
            position: absolute;
            left: 0;
            top: 0;
        }

        .call-to-action {
            text-align: center;
            margin-top: 40px;
        }

        .call-to-action h2 {
            font-size: 30px;
            font-family: 'Poppins', sans-serif;
            color: #e74c3c;
        }

        .call-to-action p {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            font-size: 18px;
            padding: 12px 40px;
            border-radius: 50px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
        }

        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .video-container {
            max-width: 800px;
            margin: 0 auto;
            margin-bottom: 40px;
        }

        .video-container iframe {
            width: 100%;
            height: 450px;
            border-radius: 10px;
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
    <!-- Header -->
    <header class="container my-5">
        <div class="section-header">
            <h1>How to Find Your Signature Scent</h1>
        </div>
    </header>

    <!-- YouTube Video Section -->
    <div class="container">
        <div class="video-container">
        <iframe src="https://www.youtube.com/embed/7pXBBmvCtJU" title="How to Find Your Signature Scent" allowfullscreen></iframe>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container content">
        <h2>Understand the Different Types of Perfumes</h2>
        <p>Choosing the right perfume can be a daunting task. Here's a guide to help you find the perfect fragrance that suits your personality and lifestyle:</p>
        <ol>
            <li><strong>Identify Your Scent Preferences:</strong> Think about whether you prefer floral, woody, oriental, or fresh scents.</li>
            <li><strong>Test Before You Buy:</strong> Always try a sample of the perfume before purchasing. Test it on your skin as fragrances can smell different on your body chemistry.</li>
            <li><strong>Consider the Season:</strong> Lighter scents are better for warmer months, while richer, spicier fragrances are more suitable for colder weather.</li>
            <li><strong>Think About the Occasion:</strong> Choose a fragrance that fits the occasion, whether it's an everyday scent, a formal event, or a night out.</li>
        </ol>

        <h3>Perfume Tips</h3>
        <ul class="tips-list">
            <li>When testing perfumes, don't smell more than three at a time to avoid overwhelming your senses.</li>
            <li>Don't rush the decision. Fragrances evolve over time, so give it a few hours to see how it develops on your skin.</li>
            <li>Perfume is an investment. A good fragrance can elevate your confidence and style.</li>
        </ul>

        <!-- Call-to-Action Section -->
        <div class="call-to-action">
            <h2>Ready to Find Your Perfect Fragrance?</h2>
            <p>Explore our collection of perfumes and discover a scent that resonates with you!</p>
            <a href="user_index.php" class="btn-gradient">Shop Now</a>
        </div><br>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
