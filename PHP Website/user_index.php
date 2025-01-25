<?php
if (!isset($_SESSION)) {
    session_start();
}

$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

  
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(":email", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();


        error_log("Fetched user data: " . print_r($user, true));
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_name'] = $user['user_name'];

            error_log("Session username: " . $_SESSION['user_name']);

            header("Location: viewProduct.php");
            exit;
        } else {
            $error_message = "Invalid email or password.";
        }
    }
}


$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];


$sql = "SELECT product_name, price, image, product_id FROM products"; // Ensure table columns are correct
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error in query: " . mysqli_error($conn));
}


$discounted_query = "SELECT * FROM products WHERE subcategory = 'discount' ORDER BY created_at";
$discounted_result = mysqli_query($conn, $discounted_query);
if (!$discounted_result) {
    die("Error fetching discounted products: " . mysqli_error($conn));
}


$cart_items = [];
if ($is_logged_in) {
    $cart_query = "SELECT product_id FROM cart_items WHERE user_id = ? AND ordered_status = 'not_ordered'";
    $cart_stmt = $conn->prepare($cart_query);
    $cart_stmt->bind_param("i", $_SESSION['user_id']);
    $cart_stmt->execute();
    $cart_result = $cart_stmt->get_result();
    while ($row = $cart_result->fetch_assoc()) {
        $cart_items[] = $row['product_id'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'] ?? '';
    $size = $_POST['size'] ?? null;
    $discounted_price = $_POST['discounted_price'];


    $image = $_FILES['image']['name'];
    $upload_dir = 'products/';
    $upload_path = $upload_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        $insert_query = "INSERT INTO products 
                         (product_name, image, description, price, stock_quantity, category, subcategory, size, discounted_price) 
                         VALUES ('$product_name', '$upload_path', '$description', $price, $stock_quantity, '$category', '$subcategory', '$size', $discounted_price)";
        if (mysqli_query($conn, $insert_query)) {
            $_SESSION['success'] = 'Product added successfully!';
            header('Location: manage_products.php');
            exit;
        } else {
            echo 'Error adding product: ' . mysqli_error($conn);
        }
    } else {
        $_SESSION['error'] = 'Error uploading image.';
    }
}
if (isset($_FILES['coupon_image'])) {
    $targetDir = "uploads/coupons/";
    $fileName = $_FILES['coupon_image']['name'];
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['coupon_image']['tmp_name'], $targetFilePath)) {
        $query = "INSERT INTO coupons (coupon_code, discount_percentage, coupon_image) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sis", $couponCode, $discountPercentage, $targetFilePath);
        $stmt->execute();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragrance Haven</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</head>

<body style="background-color:rgb(249, 249, 249);">
    <?php include 'navbar.php'; ?>

    <div class="home py-1">
        <div class="container">
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" href="user_index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about_us.php">About</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Category
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li><a class="dropdown-item" href="men_category.php">Men</a></li>
                        <li><a class="dropdown-item" href="women_category.php">Women</a></li>
                        <li><a class="dropdown-item" href="unisex_category.php">Unisex</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="delivery.php" id="deliveryLink">Delivery</a>
                    <div class="delivery-tooltip" id="deliveryTooltip">
                        <p>Delivery: Royal Express</p>

                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact_us.php">Contact</a>
                </li>
            </ul>
        </div>
    </div>



    <div id="posterCarousel" class="carousel slide my-2" data-bs-ride="carousel">
        <div class="carousel-inner">

            <div class="carousel-item active position-relative">
                <img src="images/poster333.png" class="d-block w-100" alt="Poster 3">
                <a href="add_to_cart.php" class="btn btn-primary order-now-btn position-absolute">Order Now</a>
            </div>

            <div class="carousel-item">
                <img src="images/poster111.png" class="d-block w-100" alt="Poster 1">
            </div>

            <div class="carousel-item">
                <img src="images/poster222.png" class="d-block w-100" alt="Poster 2">
            </div>
        </div>


        <button class="carousel-control-prev" type="button" data-bs-target="#posterCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#posterCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>


    <div class="container my-5">
        <h2 class="text-center mb-4" style="font-family: 'Cinzel', serif; font-weight: 600; font-size: 2rem; color: #333;">
            <b>Exclusive Coupons Just for You!</b>
        </h2>
        <div class="row row-cols-1 row-cols-md-2 g-4">

          <?php
$result = $conn->query("SELECT coupon_code, discount_percentage, coupon_image, valid_from, valid_to FROM coupons");
while ($row = $result->fetch_assoc()) {
    echo '
    <div class="col">
        <div class="card text-center shadow-sm border-0 rounded overflow-hidden">
            <img src="' . $row['coupon_image'] . '" class="card-img-top" alt="' . $row['coupon_code'] . '">
            <div class="card-body p-4">
                <h5 class="card-title mb-3">' . $row['coupon_code'] . ' Coupon</h5>
                <p class="card-text text-muted">Save ' . $row['discount_percentage'] . '%! Use code: <b>' . $row['coupon_code'] . '</b></p>
                <p class="card-text text-muted">Expires: ' . $row['valid_to'] . '</p>
            </div>
        </div>
    </div>';
}
?>

        </div>


        <div class="container my-5">
            <h2 class="text-center mb-7 mt-7" style="font-family: 'Roboto', sans-serif; font-weight: 600; color: #2c3e50;">Browse by Category</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">

                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded overflow-hidden">
                        <div class="position-relative">
                            <video class="card-img-top" autoplay muted loop style="height: 250px; object-fit: cover;">
                                <source src="videos/video8.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <a href="men_category.php" class="btn btn-light btn-lg rounded-pill">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3" style="font-family: 'Roboto', sans-serif; font-weight: 600; color: #34495e;">Men</h5>
                            <p class="card-text text-muted">Explore our collection of men's products.</p>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded overflow-hidden">
                        <div class="position-relative">
                            <video class="card-img-top" autoplay muted loop style="height: 250px; object-fit: cover;">
                                <source src="videos/video6.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <a href="women_category.php" class="btn btn-light btn-lg rounded-pill">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3" style="font-family: 'Roboto', sans-serif; font-weight: 600; color: #34495e;">Women</h5>
                            <p class="card-text text-muted">Discover elegant products for women.</p>
                        </div>
                    </div>
                </div>


                <div class="col">
                    <div class="card text-center shadow-sm border-0 rounded overflow-hidden">
                        <div class="position-relative">
                            <video class="card-img-top" autoplay muted loop style="height: 250px; object-fit: cover;">
                                <source src="videos/video7.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <a href="unisex_category.php" class="btn btn-light btn-lg rounded-pill">Shop Now <i class="fa fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3" style="font-family: 'Roboto', sans-serif; font-weight: 600; color: #34495e;">Unisex</h5>
                            <p class="card-text text-muted">Browse unisex products suitable for everyone.</p>
                        </div>
                    </div>
                </div>
            </div>



            <div class="container my-5">
                <h2 class="text-center mb-6" style="font-family: 'Cinzel', serif; font-weight: 600; font-size: 2rem; color: #333;">
                    <b>Exclusive Discounts Just for You!</b>
                </h2>
                <p class="text-center text-muted mb-3" style="font-size: 1.2rem;">
                    Don't miss out on these limited-time offers. Indulge in luxury at unbeatable prices.
                </p>
                <a href="discounted_product.php" class="btn btn-primary1 btn-see-more"><b>See More</b></a>
                <div id="discountedProductsCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        <?php
                        if ($discounted_result) {
                            $counter = 0;
                            while ($discounted_product = mysqli_fetch_assoc($discounted_result)) {
                                $stock_quantity = $discounted_product['stock_quantity'];
                                $is_sold_out = $stock_quantity == 0;

                                $image = isset($discounted_product['image']) && !empty($discounted_product['image'])
                                    ? 'products/' . htmlspecialchars($discounted_product['image'])
                                    : 'images/default-image.jpg';

                                $product_name = htmlspecialchars($discounted_product['product_name']);
                                $product_price = htmlspecialchars($discounted_product['price']);
                                $product_discounted_price = htmlspecialchars($discounted_product['discounted_price']);


                                $discount_percentage = 0;
                                if ($product_price > 0) {
                                    $discount_percentage = round((($product_price - $product_discounted_price) / $product_price) * 100);
                                }


                                if ($counter % 4 == 0) {
                                    echo $counter == 0 ? '<div class="carousel-item active">' : '<div class="carousel-item">';
                                    echo '<div class="row row-cols-1 row-cols-md-4 g-4">';
                                }
                        ?>
                                <div class="col">
                                    <div class="card h-100 text-center shadow-sm border-0 rounded product-card">
                                        <div class="image-container position-relative overflow-hidden">

                                            <?php if ($discount_percentage > 0): ?>
                                                <div class="discount-badge position-absolute top-0 start-0 bg-danger text-white px-2 py-1 rounded-end"
                                                    style="font-size: 0.9rem; z-index: 10;">
                                                    <?php echo $discount_percentage; ?>% OFF
                                                </div>
                                            <?php endif; ?>
                                            <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3"
                                                alt="<?php echo $product_name; ?>"
                                                style="height: 200px; object-fit: contain; transition: transform 0.3s ease-in-out;">

                                            <?php if ($is_sold_out): ?>
                                                <div class="position-absolute top-50 start-50 translate-middle w-100 h-100 d-flex justify-content-center align-items-center"
                                                    style="background: rgba(52, 51, 51, 0.7);">
                                                    <div class="sold-out-badge text-center bg-red px-2 py-0 rounded-pill shadow-sm"
                                                        style="color:rgb(253, 253, 255); font-weight: 550; border: 2px">
                                                        Sold Out
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease-in-out;">
                                                <?php if (!$is_sold_out): ?>
                                                    <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                                        <input type="hidden" name="product_id" value="<?php echo $discounted_product['product_id']; ?>">
                                                        <input type="hidden" name="product_name" value="<?php echo $product_name; ?>">
                                                        <input type="hidden" name="product_price" value="<?php echo $product_discounted_price; ?>">
                                                        <input type="hidden" name="product_image" value="<?php echo $image; ?>">
                                                        <button type="submit" name="add_to_cart" class="btn btn-outline-light btn-sm" <?php echo in_array($discounted_product['product_id'], $cart_items) ? 'disabled' : ''; ?>>
                                                            <i class="fa fa-cart-plus"></i>
                                                        </button>
                                                        <a href="product_details.php?product_id=<?php echo $discounted_product['product_id']; ?>" class="btn btn-light btn-sm">
                                                            <i class="fa fa-info-circle"></i>
                                                        </a>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>
                                            <div class="pricing mb-3">
                                                <h6 class="normal-price text-muted">
                                                    <del>$<?php echo number_format($product_price, 2); ?></del>
                                                </h6>
                                                <h6 class="discount-price text-danger fw-bold">
                                                    $<?php echo number_format($product_discounted_price, 2); ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        <?php
                                $counter++;

                                if ($counter % 4 == 0 || $counter == mysqli_num_rows($discounted_result)) {
                                    echo '</div></div>';
                                }
                            }
                        } else {
                            echo '<p class="text-center text-muted">No discounted products found.</p>';
                        }
                        ?>
                    </div>



                    <button class="carousel-control-prev1" type="button" data-bs-target="#discountedProductsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next1" type="button" data-bs-target="#discountedProductsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div><br><br>


                <h2 class="text-center mb-4" style="font-family: 'Cinzel', serif; font-weight: 600; font-size: 2rem; color: #333;">
                    <b>Step Into Our World of Elegance</b>
                </h2>
                <p class="text-center text-muted mb-2" style="font-size: 1.2rem;">
                    Be the first to explore our latest collections. Fresh scents and stunning designs await you!
                </p>

                <div class="latest mb-3 d-flex align-items-center">
                    <div class="video-advertisement mb-5 text-center">
                        <video autoplay muted loop class="shadow rounded" style="width: 100%;  border-radius: 15px; object-fit: cover; height:360px; margin-right:230px; margin-top:30px;">
                            <source src="./videos/video5.mp4" type="video/mp4">
                        </video>
                    </div>
                    <div class="text-left" style="width: 50%; margin-left:50px;">
                        <h3><b>Popular</b></h3>
                        <p class="text-muted mt-1">Experience the essence of our popular scent</p>
                        <a href="popular_product.php" class="btn btn-primary mt-1">See More</a>
                    </div>
                </div>

                <div class="latest mb-1 d-flex align-items-center">
                    <div class="text-right" style="width: 50%; margin-left:120px;">
                        <h3><b>New Arrival</b></h3>
                        <p class="text-muted mt-2">Experience the essence of our new arrival scent</p>

                        <a href="latest_product.php" class="btn btn-primary mt-1">See More</a>
                    </div>

                    <div class="video-advertisement mb-5 text-center">
                        <video autoplay muted loop class="shadow rounded" style="width: 100%;  border-radius: 15px; object-fit: cover; height:400px; margin-right:230px;">
                            <source src="./videos/video2.mp4" type="video/mp4">
                        </video>
                    </div>
                </div>

                <h2 class="mb-2 mt-2" style="font-family: 'Roboto', sans-serif; font-weight: 500; "><b>Featured Products</b></h2>
                <a href="featured_product.php" class="btn btn-primary1 btn-see-more"><b>See More </b></a>
                <div id="featuredProductsCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        <?php
                        $featured_query = "SELECT * FROM products WHERE subcategory = 'featured' ORDER BY created_at";
                        $featured_result = mysqli_query($conn, $featured_query);

                        $counter = 0;
                        while ($featured_product = mysqli_fetch_assoc($featured_result)) {
                            $stock_quantity = $featured_product['stock_quantity'];
                            $is_sold_out = $stock_quantity == 0;

                            $image = isset($featured_product['image']) && !empty($featured_product['image'])
                                ? 'products/' . htmlspecialchars($featured_product['image'])
                                : 'images/default-image.jpg';

                            $product_name = htmlspecialchars($featured_product['product_name']);
                            $product_price = htmlspecialchars($featured_product['price']);

                            if ($counter % 4 == 0) {
                                echo $counter == 0 ? '<div class="carousel-item active">' : '<div class="carousel-item">';
                                echo '<div class="row row-cols-1 row-cols-md-4 g-4">';
                            }
                        ?>
                            <div class="col">
                                <div class="card h-100 text-center shadow-sm border-0 rounded product-card">
                                    <div class="image-container position-relative overflow-hidden">
                                        <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3"
                                            alt="<?php echo $product_name; ?>"
                                            style="height: 200px; object-fit: contain; transition: transform 0.3s ease-in-out;">


                                        <?php if ($is_sold_out): ?>
                                            <div class="position-absolute top-50 start-50 translate-middle w-100 h-100 d-flex justify-content-center align-items-center"
                                                style="background: rgba(52, 51, 51, 0.7);">
                                                <div class="sold-out-badge text-center bg-red px-2 py-0 rounded-pill shadow-sm"
                                                    style="color:rgb(253, 253, 255); font-weight: 550; border: 2px">
                                                    Sold Out
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Hover Overlay -->
                                        <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                                            style="background: rgba(0, 0, 0, 0.5); opacity: 0; transition: opacity 0.3s ease-in-out;">
                                            <?php if (!$is_sold_out): ?>
                                                <form method="POST" action="add_to_cart.php" class="d-flex gap-2">
                                                    <input type="hidden" name="product_id" value="<?php echo $featured_product['product_id']; ?>">
                                                    <button type="submit" name="add_to_cart" class="btn btn-outline-light btn-sm">
                                                        <i class="fa fa-cart-plus"></i>
                                                    </button>
                                                    <a href="product_details.php?product_id=<?php echo $featured_product['product_id']; ?>" class="btn btn-light btn-sm">
                                                        <i class="fa fa-info-circle"></i>
                                                    </a>
                                                </form>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>
                                        <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $counter++;
                            if ($counter % 4 == 0 || $counter == mysqli_num_rows($featured_result)) {
                                echo '</div></div>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <button class="carousel-control-prev1" type="button" data-bs-target="#featuredProductsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next1" type="button" data-bs-target="#featuredProductsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div><br>
            <script>
                const productCards = document.querySelectorAll('.product-card .image-container');
                productCards.forEach(card => {
                    card.addEventListener('mouseover', () => {
                        card.querySelector('.hover-overlay').style.opacity = '1';
                        card.querySelector('img').style.transform = 'scale(1.1)';
                    });

                    card.addEventListener('mouseout', () => {
                        card.querySelector('.hover-overlay').style.opacity = '0';
                        card.querySelector('img').style.transform = 'scale(1)';
                    });
                });
            </script>


            <div class="video-advertisement mb-5 text-center">
                <h2 class="fw-bold text-black mb-3">Step into the World of Luxury</h2>
                <p class="text-muted mb-4">Discover the allure of our premium fragrances through this captivating visual journey.</p>
                <video autoplay muted loop class="shadow rounded" style="width: 100%; max-width: 2200px; border-radius: 15px; object-fit: cover; height: 320px;">
                    <source src="./videos/videoplayback1.mp4" type="video/mp4">
                </video>
            </div>


            <section class="related-resources py-2">
                <div class="container">
                    <h3 class="text-center mb-2 display-5 font-weight-bold">Related Resources</h3>
                    <div class="row">

                        <div class="col-md-3 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="images/resource1.jpg" class="card-img-top">
                                <div class="card-body d-flex flex-column justify-content-between text-center">
                                    <h5 class="card-title font-weight-bold">How to Spray Perfume</h5>
                                    <p class="card-text text-muted">Learn the best ways to apply perfume throughout the day for lasting fragrance.</p>
                                    <a href="how_to_spray_perfume.php" class="btn btn-gradient-primary w-100">Read More</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="images/resource2.jpg" class="card-img-top">
                                <div class="card-body d-flex flex-column justify-content-between text-center">
                                    <h5 class="card-title font-weight-bold">How to Find Your Signature Scent</h5>
                                    <p class="card-text text-muted">A guide to help you find the perfect fragrance for your personality and lifestyle.</p>
                                    <a href="choosing_right_perfume.php" class="btn btn-gradient-primary w-100">Read More</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="images/resource3.jpg" class="card-img-top">
                                <div class="card-body d-flex flex-column justify-content-between text-center">
                                    <h5 class="card-title font-weight-bold">Perfume Tips for Different Occasions</h5>
                                    <p class="card-text text-muted">Tips on selecting the perfect scent for work, date, and more.</p>
                                    <a href="perfume_tips_occasion.php" class="btn btn-gradient-primary w-100">Read More</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-4">
                            <div class="card h-100 shadow-sm border-0">
                                <img src="images/resource4.jpg" class="card-img-top" alt="Perfume Storage Guide">
                                <div class="card-body d-flex flex-column justify-content-between text-center">
                                    <h5 class="card-title font-weight-bold">Perfume Storage Guide</h5>
                                    <p class="card-text text-muted">How to store your perfumes properly to maintain their fragrance and quality.</p>
                                    <a href="perfume_storage.php" class="btn btn-gradient-primary w-100">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>

<?php

mysqli_close($conn);
?>