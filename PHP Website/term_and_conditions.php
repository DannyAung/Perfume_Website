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
    <title>Delivery Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
    <style>
        body {
            background-color: #f9f9f9;
        }

        .terms-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .terms-container h2 {
            text-align: center;
            color: #007bff;
            font-size: 2.5rem;
        }

        .terms-container h4 {
            color: #343a40;
            margin-top: 20px;
            font-size: 1.5rem;
        }

        .terms-container p {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.7;
        }

        .terms-container ul {
            list-style-type: none;
            padding-left: 0;
        }

        .terms-container ul li {
            font-size: 1.1rem;
            color: #555;
        }

        .terms-container ul li::before {
            content: "\2022";
            color: #007bff;
            font-weight: bold;
            display: inline-block;
            width: 1rem;
            margin-left: -1rem;
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
    <div class="container">
        <div class="terms-container">
            <h2>Terms and Conditions</h2>
            <p>Welcome to Fragrance Haven. These Terms and Conditions outline the rules and regulations for the use of our website and services. By using this website, you agree to comply with these terms. Please read them carefully.</p>

            <h4>1. General Information</h4>
            <p>These Terms and Conditions govern the use of the website, including but not limited to the purchasing of products, registration, and other services provided through the website.</p>

            <h4>2. Intellectual Property</h4>
            <p>All content on the website, including text, images, graphics, logos, and trademarks, are owned by Fragrance Haven. Unauthorized use of the website’s content is prohibited.</p>

            <h4>3. Use of the Website</h4>
            <p>By using this website, you agree to:</p>
            <ul>
                <li>Comply with all applicable laws and regulations</li>
                <li>Not misuse the website for illegal or unauthorized purposes</li>
                <li>Not attempt to harm the functionality of the website or its security features</li>
            </ul>

            <h4>4. Registration and Account</h4>
            <p>To use certain features of the website, you may be required to create an account. You agree to provide accurate information during registration and to keep your account details secure.</p>

            <h4>5. Orders and Payment</h4>
            <p>By placing an order on the website, you agree to pay the total price of the products and any applicable taxes and fees. We reserve the right to cancel or refuse an order under certain circumstances.</p>

            <h4>6. Shipping and Delivery</h4>
            <p>We will make every effort to deliver the products within the estimated time frame. However, delivery times may vary, and we are not responsible for any delays caused by external factors such as weather or shipping carrier issues.</p>

            <h4>7. Return and Refund Policy</h4>
            <p>Our return and refund policy allows you to return products within a specified period after purchase. Please refer to our <a href="return_policy.php">Return Policy</a> for detailed information.</p>

            <h4>8. Privacy Policy</h4>
            <p>We respect your privacy and are committed to protecting your personal information. Please refer to our <a href="privacy_policy.php">Privacy Policy</a> for details on how we collect and use your data.</p>

            <h4>9. Limitation of Liability</h4>
            <p>[Your Company Name] will not be liable for any damages, losses, or expenses arising from the use or inability to use the website or the products purchased through it, including but not limited to indirect, incidental, or consequential damages.</p>

            <h4>10. Changes to the Terms and Conditions</h4>
            <p>We reserve the right to update these Terms and Conditions at any time. Any changes will be posted on this page, and the updated terms will be effective as of the date of posting.</p>

            <h4>11. Governing Law</h4>
            <p>These Terms and Conditions will be governed by and construed in accordance with the laws of [Your Country]. Any disputes arising from these terms will be subject to the exclusive jurisdiction of the courts in [Your Country].</p>

            <h4>12. Contact Information</h4>
            <p>If you have any questions about these Terms and Conditions, please contact us at:</p>
            <p><b>Email:</b> [fragrancehaven@gmail.com]</p>
            <p><b>Phone:</b> [+959450197415]</p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>