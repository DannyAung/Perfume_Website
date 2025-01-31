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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <style>
    </style>
</head>

</head>

<body>
    <?php include 'navbar.php'; ?>


    <div class="container mt-5">
        <h2 class="text-center mb-4" style="font-size: 2.5rem; color: #343a40; font-weight: 600;">Delivery Information</h2>
        <p class="text-center mb-5" style="font-size: 1.1rem; color: #555;">
            We offer fast and reliable delivery for your convenience.
        </p>


        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-light">
                    <div class="card-body">
                        <h4 class="card-title text-center" style="font-size: 1.5rem; color:rgb(23, 79, 139);">Delivery Timeframes</h4>
                        <ul class="list-unstyled">
                            <li style="font-size: 1.1rem; margin-bottom: 10px;">
                                Standard [Yangon]:<span style="color:rgb(1, 2, 10);">Within 2-3 days</span>
                            </li>
                            <li style="font-size: 1.1rem; margin-bottom: 10px;">
                                Standard [Other Cities]: <span style="color:rgb(14, 24, 16);">Within 3-5 days</span>
                            </li>
                            <li style="font-size: 1.1rem; margin-bottom: 10px;">
                                Express [Yangon/Other Cities]: <span style="color: #ff5733;">Within 1 day</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-5">
            <div class="col-lg-8">
                <div class="card shadow-sm border-light">
                    <div class="card-body">
                        <h4 class="card-title text-center" style="font-size: 1.5rem; color:rgb(12, 70, 132);">How Delivery Works</h4>
                        <p style="font-size: 1.1rem; color: #555;">
                            Once your order is placed, we process it and dispatch it as soon as possible. You can track the order by checking order status from your order history.
                        </p>
                        <p style="font-size: 1.1rem; color: #555;">
                            If you have any questions or concerns about delivery, feel free to <a href="contact_us.php" class="text-decoration-none" style="color: #007bff;">contact us</a>!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div><br>
    <?php include 'footer.php'; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>