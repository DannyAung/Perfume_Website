<?php

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
<?php include 'navbar.php'; ?>

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
