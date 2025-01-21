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
    <title>Perfume Tips for Different Occasions | Your Perfume Website</title>
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

        .occasion-list {
            list-style-type: none;
            padding-left: 0;
        }

        .occasion-list li {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            padding-left: 25px;
            position: relative;
        }

        .occasion-list li::before {
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
            <h1>Perfume Tips for Different Occasions</h1>
        </div>
    </header>
 <!-- YouTube Video Section -->
 <div class="container">
        <div class="video-container">
        <iframe src="https://www.youtube.com/embed/TVOl7Ct0Ch8" title="How to Find Your Signature Scent" allowfullscreen></iframe>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container content">
        <p>Choosing the right perfume for different occasions can enhance your experience and leave a lasting impression. Here are some tips for selecting the perfect fragrance depending on the event:</p>

        <h2>1. Daily Wear</h2>
        <p>For everyday use, opt for lighter, fresher scents that won't overwhelm your senses. Think about floral, citrus, or aquatic notes.</p>
        
        <h2>2. Office Wear</h2>
        <p>Choose subtle, sophisticated fragrances that aren’t too strong. Soft florals, light woods, and fresh citrus blends work well for a professional setting.</p>

        <h2>3. Date Night</h2>
        <p>Romantic evenings call for a more alluring scent. Opt for fragrances with warm, sensual notes like vanilla, amber, or musk.</p>

        <h2>4. Special Occasions</h2>
        <p>For a formal event or a wedding, go for a more luxurious scent. Elegant florals or deep, exotic fragrances with notes of jasmine or rose can be a great option.</p>

        <h2>5. Outdoor Activities</h2>
        <p>During outdoor events or casual outings, fresh and invigorating fragrances work best. Citrus, green, or aquatic notes are perfect for the occasion.</p>

        <h3>Perfume Tips</h3>
        <ul class="occasion-list">
            <li>Spray your perfume on pulse points (wrists, neck, behind ears) for better projection.</li>
            <li>Don’t overdo it! A little goes a long way, especially with stronger fragrances.</li>
            <li>Consider layering fragrances with matching body lotions for a longer-lasting effect.</li>
        </ul>

        <!-- Call-to-Action Section -->
        <div class="call-to-action">
            <h2>Ready to Find the Perfect Fragrance?</h2>
            <p>Explore our collection of perfumes designed for every occasion. Find the scent that suits your style and personality!</p>
            <a href="user_index.php" class="btn-gradient">Shop Now</a>
        </div><br>
    </div>

    <?php include 'footer.php'; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
   

</body>
</html>
