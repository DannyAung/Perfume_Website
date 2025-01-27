<?php
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
<?php include 'navbar.php'; ?>

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