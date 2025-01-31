<?php

session_start();
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</head>

<body>
<?php include 'navbar.php'; ?>

    <header class="bg-light py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Welcome to Fragrance Haven</h1>
            <p class="lead text-muted">Discover the art of fine fragrances that inspire confidence, beauty, and elegance.</p>
        </div>
    </header>

    
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4">
                    <img src="./images/about_us.jpg" alt="About Us" class="img-fluid rounded">
                </div>
                <div class="col-md-6">
                    <h2 class="mb-4">Who We Are</h2>
                    <p class="text-muted">Fragrance Haven is your ultimate destination for premium perfumes. We are dedicated to providing our customers with high-quality fragrances sourced from around the globe. Our mission is to enhance your personality and leave a lasting impression wherever you go.</p>
                    <p class="text-muted">Whether you're looking for a signature scent or a gift for a loved one, our carefully curated collection offers something for everyone.</p>
                </div>
            </div>
        </div>
    </section>

   
    <section class="bg-light py-5">
        <div class="container text-center">
            <h2 class="mb-4">Our Mission</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Our mission is to make luxury fragrances accessible to everyone. We believe in empowering individuals by helping them express themselves through the timeless art of scent. At Fragrance Haven, every scent tells a story — and we’re here to help you create yours.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Why Choose Us?</h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <i class="bi bi-gem fs-1 text-primary mb-3"></i>
                    <h5>Premium Quality</h5>
                    <p class="text-muted">We ensure every product meets the highest standards of quality and authenticity.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-cart-check fs-1 text-success mb-3"></i>
                    <h5>Wide Selection</h5>
                    <p class="text-muted">Choose from a wide range of fragrances tailored to suit every taste and occasion.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-people fs-1 text-warning mb-3"></i>
                    <h5>Customer Focused</h5>
                    <p class="text-muted">We prioritize your satisfaction and strive to provide an exceptional shopping experience.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
