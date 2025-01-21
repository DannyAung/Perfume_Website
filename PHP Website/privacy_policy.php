<?php
// Include the necessary files, such as header and session management
session_start();
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
        }

        .privacy-policy-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .privacy-policy-container h2 {
            text-align: center;
            color: #007bff;
            font-size: 2.5rem;
        }

        .privacy-policy-container h4 {
            color: #343a40;
            margin-top: 20px;
            font-size: 1.5rem;
        }

        .privacy-policy-container p {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.7;
        }

        .privacy-policy-container ul {
            list-style-type: none;
            padding-left: 0;
        }

        .privacy-policy-container ul li {
            font-size: 1.1rem;
            color: #555;
        }

        .privacy-policy-container ul li::before {
            content: "\2022";
            color: #007bff;
            font-weight: bold;
            display: inline-block;
            width: 1rem;
            margin-left: -1rem;
        }
    </style>
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
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
    <div class="container">
        <div class="privacy-policy-container">
            <h2>Privacy Policy</h2>
            <p>At Fragrance Haven, we value the privacy of our customers and are committed to protecting your personal information. This privacy policy explains how we collect, use, and safeguard your data when you interact with our website and services.</p>

            <h4>1. Information We Collect</h4>
            <p>We collect the following types of information to improve our services and provide a better experience for you:</p>
            <ul>
                <li>Personal identification information (e.g., name, email address, phone number)</li>
                <li>Transaction data (e.g., order history, payment details)</li>
                <li>Device information (e.g., IP address, browser type, device type)</li>
            </ul>

            <h4>2. How We Use Your Information</h4>
            <p>Your personal data is used for the following purposes:</p>
            <ul>
                <li>To process and fulfill your orders</li>
                <li>To communicate with you about your orders and account</li>
                <li>To send promotional emails (if opted in)</li>
                <li>To improve our website and services</li>
                <li>To personalize your experience on the website</li>
            </ul>

            <h4>3. Sharing Your Information</h4>
            <p>We will never sell, rent, or trade your personal information. However, we may share your information with trusted third-party partners under the following circumstances:</p>
            <ul>
                <li>For order processing and shipping services</li>
                <li>For analytics and website performance purposes</li>
                <li>When required by law or to protect our legal rights</li>
            </ul>

            <h4>4. Security of Your Information</h4>
            <p>We take reasonable measures to protect your personal information, including the use of encryption technologies, secure servers, and firewalls. However, no method of transmission over the internet or electronic storage is completely secure, and we cannot guarantee absolute security.</p>

            <h4>5. Your Rights and Choices</h4>
            <p>You have the right to:</p>
            <ul>
                <li>Access the personal data we hold about you</li>
                <li>Request correction of any inaccurate or incomplete information</li>
                <li>Request deletion of your personal data (subject to legal obligations)</li>
                <li>Opt-out of receiving marketing communications</li>
            </ul>

            <h4>7. Changes to This Privacy Policy</h4>
            <p>We may update this privacy policy from time to time. Any changes will be posted on this page, and the updated policy will be effective as of the date of posting.</p>

            <h4>8. Contact Us</h4>
            <p>If you have any questions or concerns about this privacy policy, please contact us at:</p>
            <p><b>Email:</b> [fragrancehaven@gmail.com]</p>
            <p><b>Phone:</b> [+959450197415]</p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>
