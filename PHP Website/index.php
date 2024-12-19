<?php
require_once 'db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'] ?? '';
    $size = $_POST['size'] ?? null; // Optional size
    $discounted_price = $_POST['discounted_price']; 

    // Handle image upload
    $image = $_FILES['image']['name'];
    $upload_dir = 'products/';
    $upload_path = $upload_dir . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
        try {
            $sql = "INSERT INTO products 
                    (product_name, image, description, price, stock_quantity, category, subcategory, size, discounted_price) 
                    VALUES (:product_name, :image, :description, :price, :stock_quantity, :category, :subcategory, :size, :discounted_price)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':product_name' => $product_name,
                ':image' => $upload_path,
                ':description' => $description,
                ':price' => $price,
                ':stock_quantity' => $stock_quantity,
                ':category' => $category,
                ':subcategory' => $subcategory,
                ':size' => $size, 
                ':discounted_price' => $discounted_price,
            ]);
            $_SESSION['success'] = 'Product added successfully!';
            header('Location: manage_products.php');
            exit;
        } catch (PDOException $e) {
            echo 'Error adding product: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'Error uploading image.';
    }
}
?>



<?php
// Connect to the database
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3307; // Update with the correct port

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch popular products
$sql = "SELECT product_name, price, image FROM products"; // Ensure table columns are correct
$result = mysqli_query($conn, $sql);

// Query to fetch discounted products for the user index page
$discounted_query = "SELECT * FROM products WHERE subcategory = 'discount' ORDER BY created_at DESC LIMIT 4";
$discounted_result = mysqli_query($conn, $discounted_query);

if (!$discounted_result) {
    die("Error fetching discounted products: " . mysqli_error($conn));
}


if (!$result) {
    die("Error in query: " . mysqli_error($conn));
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
    <!-- Bootstrap JS -->
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="./images/Logo.png" alt="Logo" style="width:50px; height:auto;">
            <b class="ms-2 dm-serif-display-regular-italic custom-font-color">FRAGRANCE  HAVEN</b>
        </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex w-100 align-items-center">
        <!-- Center the Search Bar -->
                <div class="mx-auto">
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
                
        <!-- Login and Cart Buttons on the Right -->
        <div class="LoginCart">
    <a href="user_login.php" class="btn login-btn">Login/Register</a>
    <a href="cart.php" class="btn cart-btn" id="cart-button">
        <img src="./images/cart-icon.jpg" alt="Cart" style="width:20px; margin-right:6px;">
        Cart 
    </a>
    </div>
         </div>
             </div>

</nav>

    <!-- New Navigation Links Section -->
    <div class="py-1">
        <div class="container">
            <ul class="nav justify-content">
                <li class="nav-item">
                    <a class="nav-link active" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">
                    Category
                </a>
                <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                    <li><a class="dropdown-item" href="#">Men</a></li>
                    <li><a class="dropdown-item" href="#">Women</a></li>
                    <li><a class="dropdown-item" href="#">Unisex</a></li>
                </ul>
            </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Delivery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </div>

<!-- Carousel -->
<div id="posterCarousel" class="carousel slide my-2" data-bs-ride="carousel">
    <div class="carousel-inner">
        
        <div class="carousel-item active position-relative">
            <img src="images/poster333.png" class="d-block w-100" alt="Poster 3"> 
            <a href="cart.php" class="btn btn-primary order-now-btn">Order Now</a>
        </div>
        
        <div class="carousel-item">
            <img src="images/poster111.png" class="d-block w-100" alt="Poster 1">
        </div>
      
        <div class="carousel-item">
            <img src="images/poster222.png" class="d-block w-100" alt="Poster 2">
        </div>
    </div>

    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#posterCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#posterCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>


<!-- Product Grid -->
<div class="container my-5">
<h1 class="text-center mb-4"><b>Our Products</b></h1>

<h2 class="text-center mb-4 mt-5">Discounted Products</h2>
<div class="row row-cols-1 row-cols-md-4 g-4">
    <?php 
    if ($discounted_result) {
        while ($discounted_product = mysqli_fetch_assoc($discounted_result)) {
            // Image path logic
            $image = isset($discounted_product['image']) && !empty($discounted_product['image']) 
                ? 'products/' . htmlspecialchars($discounted_product['image']) 
                : 'images/default-image.jpg';

            // Get product details
            $product_name = htmlspecialchars($discounted_product['product_name']);
            $product_price = htmlspecialchars($discounted_product['price']);
            $product_discounted_price = htmlspecialchars($discounted_product['discounted_price']);
    ?>
        <div class="col">
            <div class="card h-100 text-center shadow-sm border-0 rounded">
                <!-- Image container with dynamic height and hover effect -->
                <div class="image-container" style="overflow: hidden; position: relative; margin: 0 auto;">
                    <img src="<?php echo $image; ?>" class="card-img-top img-fluid p-3" 
                    alt="<?php echo $product_name; ?>" 
                    style="height: 150px; object-fit: contain; transition: transform 0.3s;">
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <!-- Product Name -->
                    <h5 class="card-title text-truncate"><?php echo $product_name; ?></h5>

                    <!-- Pricing Section -->
                    <div class="pricing mb-3">
                        <h6 class="normal-price text-muted">
                            <h7>Normal Price</h7><br>
                            <del>$<?php echo number_format($product_price, 2); ?></del>
                        </h6>

                        <h7>Sale Price</h7><br>
                        <h6 class="discount-price text-danger fw-bold">
                            $<?php echo number_format($product_discounted_price, 2); ?>
                        </h6>
                    </div>

                    <!-- Buttons Section -->
                    <form method="POST" action="index.php" class="d-flex gap-2">
                        <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm flex-grow-1">
                            Add to Cart
                        </button>
                        <a href="product_details.php?product_id=<?php echo $discounted_product['product_id']; ?>" 
                        class="btn btn-primary btn-sm flex-grow-1">
                            View Details
                        </a>
                    </form>
                </div>
            </div>
        </div>
    <?php 
        }
    } else {
        echo '<p class="text-center text-muted">No discounted products found.</p>';
    }
    ?>
</div>

    
<h2 class="text-center mb-4 mt-5">Popular Products</h2>
    <!-- Popular Products Section -->
    <div class="row row-cols-1 row-cols-md-4 g-4">
    <?php 
    // Query to fetch popular products based on subcategory
    $popular_query = "SELECT * FROM products WHERE subcategory = 'popular' ORDER BY created_at DESC LIMIT 4";
    $popular_result = mysqli_query($conn, $popular_query);
        
    while ($popular_product = mysqli_fetch_assoc($popular_result)) {
        // Image path 
        $image = isset($popular_product['image']) && !empty($popular_product['image']) 
            ? 'products/' . htmlspecialchars($popular_product['image']) 
            : 'images/default-image.jpg';

       
        if (!file_exists($image)) {
            $image = 'images/default-image.jpg';  
        }
    
        $product_name = htmlspecialchars($popular_product['product_name']);
        $product_price = htmlspecialchars($popular_product['price']);
        
    ?>

    <div class="col">
    <div class="card h-100 text-center shadow d-flex flex-column">
        <!-- Image container with dynamic height -->
        <div class="image-container" style="width: auto; margin: 0 auto;">
            <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($popular_product['product_name']); ?>" 
            style="max-height: 150px; width: 100%; object-fit: contain;">
        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php echo $product_name; ?></h5>
            <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
            
            <form method="POST" action="index.php" class="d-flex justify-content-between">
                <div class="w-50 pr-2">
                    <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm w-100">Add to Cart</button>
                </div>
                <div class="w-50 pl-2">
                    <a href="product_details.php?product_id=<?php echo $popular_product['product_id']; ?>" class="btn btn-primary btn-sm">
                        View Details
                    </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
</div>
    

    
    <!-- Latest Products Section -->
    <h2 class="text-center mb-4 mt-5">Latest Products</h2>
     <div class="row row-cols-1 row-cols-md-4 g-4">
    <?php 
    // Query to fetch popular products based on subcategory
    $latest_query = "SELECT * FROM products WHERE subcategory = 'latest' ORDER BY created_at DESC LIMIT 4";
    $latest_result = mysqli_query($conn, $latest_query);
        
    while ($latest_product = mysqli_fetch_assoc($latest_result)) {
        // Image path logic
        $image = isset($latest_product['image']) && !empty($latest_product['image']) 
            ? 'products/' . htmlspecialchars($latest_product['image']) 
            : 'images/default-image.jpg';

        // Check if image exists, otherwise fallback
        if (!file_exists($image)) {
            $image = 'images/default-image.jpg';  // Fallback image
        }
    
        $product_name = htmlspecialchars($latest_product['product_name']);
        $product_price = htmlspecialchars($latest_product['price']);
    ?>
        <div class="col">
    <div class="card h-100 text-center shadow d-flex flex-column">
        <!-- Image container with dynamic height -->
        <div class="image-container" style="width: auto; margin: 0 auto;">
            <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($latest_product['product_name']); ?>" 
            style="max-height: 150px; width: 100%; object-fit: contain;">
        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php echo $product_name; ?></h5>
            <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
            
            <form method="POST" action="index.php" class="d-flex justify-content-between">
                <div class="w-50 pr-2">
                    <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm w-100">Add to Cart</button>
                </div>
                <div class="w-50 pl-2">
                    <a href="product_details.php?product_id=<?php echo $latest_product['product_id']; ?>" class="btn btn-primary btn-sm">
                        View Details
                    </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
</div>
    
    <!-- Featured Products Section -->
    <h2 class="text-center mb-4 mt-5">Featured Products</h2>
    <div class="row row-cols-1 row-cols-md-4 g-4">
    <?php 
    // Query to fetch popular products based on subcategory
    $featured_query = "SELECT * FROM products WHERE subcategory = 'featured' ORDER BY created_at DESC LIMIT 4";
    $featured_result = mysqli_query($conn, $featured_query);
        
    while ($featured_product = mysqli_fetch_assoc($featured_result)) {
        // Image path logic
        $image = isset($featured_product['image']) && !empty($featured_product['image']) 
            ? 'products/' . htmlspecialchars($featured_product['image']) 
            : 'images/default-image.jpg';

        // Check if image exists, otherwise fallback
        if (!file_exists($image)) {
            $image = 'images/default-image.jpg';  // Fallback image
        }
    
        $product_name = htmlspecialchars($featured_product['product_name']);
        $product_price = htmlspecialchars($featured_product['price']);
    ?>
        <div class="col">
    <div class="card h-100 text-center shadow d-flex flex-column">
        <!-- Image container with dynamic height -->
        <div class="image-container" style="width: auto; margin: 0 auto;">
            <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($featured_product['product_name']); ?>" 
            style="max-height: 150px; width: 100%; object-fit: contain;">
        </div>
        <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php echo $product_name; ?></h5>
            <p class="card-text text-muted">$<?php echo number_format($product_price, 2); ?></p>
            <form method="POST" action="index.php" class="d-flex justify-content-between">
                <div class="w-50 pr-2">
                    <button type="submit" name="add_to_cart" class="btn btn-outline-primary btn-sm w-100">Add to Cart</button>
                </div>
                <div class="w-50 pl-2">
                    <a href="product_details.php?product_id=<?php echo $featured_product['product_id']; ?>" class="btn btn-primary btn-sm">
                        View Details
                    </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
</div>


<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php
// Close the database connection
mysqli_close($conn);
?>
