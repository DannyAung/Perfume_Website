<?php
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>How to Spray Perfume | Your Perfume Website</title>
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
            <h1>How to Spray Perfume Effectively</h1>
        </div>
    </header>

    <!-- YouTube Video Section -->
    <div class="container">
        <div class="video-container">
            <iframe src="https://www.youtube.com/embed/KTrgDAlkt4E" title="How to Spray Perfume" allowfullscreen></iframe>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container content">
        <h2>Step-by-Step Guide</h2>
        <p>Learn the best techniques for applying perfume to ensure a lasting fragrance. Follow these steps to enhance your fragrance application:</p>
        <ol>
            <li><strong>Prep Your Skin:</strong> Apply perfume on clean, moisturized skin. This helps the fragrance last longer.</li>
            <li><strong>Spray on Pulse Points:</strong> Target areas like the wrist, behind the ears, and the base of your throat for a longer-lasting scent.</li>
            <li><strong>Don’t Rub:</strong> Avoid rubbing your wrist together after spraying. This can break down the scent molecules.</li>
            <li><strong>Keep a Safe Distance:</strong> Hold the perfume bottle about 6-8 inches away from your skin for an even spray.</li>
        </ol>

        <h3>Perfume Tips</h3>
        <ul class="tips-list">
            <li>For a more intense fragrance, consider spraying on your clothes as well.</li>
            <li>Choose the right fragrance for the occasion: lighter scents for daytime and richer scents for evening.</li>
            <li>Store your perfume in a cool, dark place to preserve its longevity.</li>
        </ul>

        <!-- Call-to-Action Section -->
        <div class="call-to-action">
            <h2>Ready to Choose Your Perfect Scent?</h2>
            <p>Explore our wide collection of perfumes and find your new favorite fragrance today!</p>
            <a href="user_index.php" class="btn-gradient">Shop Now</a>
        </div><br>
    </div>
    <?php include 'footer.php'; ?>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>

</html>