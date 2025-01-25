<?php
// Start session
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = htmlspecialchars($_POST['product_name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);


    $category = htmlspecialchars($_POST['category'] ?? '');
    $subcategory = htmlspecialchars($_POST['subcategory'] ?? '');

    $size = htmlspecialchars($_POST['size'] ?? null);
    $discount_available = $_POST['discount_available'];
    $discount_percentage = $_POST['discount_percentage'];
    $discounted_price = $_POST['discounted_price'];


    $upload_dir = 'products/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $upload_errors = [];

    function handle_image_upload($image_key, $upload_dir, $allowed_types)
    {
        if (isset($_FILES[$image_key]) && $_FILES[$image_key]['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$image_key]['tmp_name'];
            $file_name = preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES[$image_key]['name'])); // Sanitize file name
            $file_type = mime_content_type($file_tmp);


            if (!in_array($file_type, $allowed_types)) {
                return ["error" => "$file_name is not a valid image file."];
            }


            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $file_path)) {
                return ["path" => $file_name];
            } else {
                return ["error" => "Failed to upload $file_name."];
            }
        }
        return ["path" => null];
    }

    $image_result = handle_image_upload('image', $upload_dir, $allowed_types);
    $extra_image_1_result = handle_image_upload('extra_image_1', $upload_dir, $allowed_types);
    $extra_image_2_result = handle_image_upload('extra_image_2', $upload_dir, $allowed_types);

    if (isset($image_result['error'])) {
        $upload_errors[] = $image_result['error'];
    }
    if (isset($extra_image_1_result['error'])) {
        $upload_errors[] = $extra_image_1_result['error'];
    }
    if (isset($extra_image_2_result['error'])) {
        $upload_errors[] = $extra_image_2_result['error'];
    }

    if (!empty($upload_errors)) {
        foreach ($upload_errors as $error) {
            echo "<p class='text-danger'>$error</p>";
        }
        exit;
    }

    try {
        $sql = "INSERT INTO products 
        (product_name, image, extra_image_1, extra_image_2, description, price, stock_quantity, category, subcategory, size, discount_available, discount_percentage, discounted_price) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssssdissssdi',
            $product_name,
            $image_result['path'],
            $extra_image_1_result['path'],
            $extra_image_2_result['path'],
            $description,
            $price,
            $stock_quantity,
            $category, 
            $subcategory,
            $size,
            $discount_available,
            $discount_percentage,
            $discounted_price
        );


        $stmt->execute();
        header('Location: manage_products.php');
        exit;
    } catch (mysqli_sql_exception $e) {
        echo '<p class="text-danger">Error adding product: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}



?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add Product</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 900px;
            padding: 20px;
            border: none;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 12px;
        }

        .btn-primary {
            font-size: 1.0rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .file-upload {
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px dashed #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            height: 200px;
            text-align: center;
            position: relative;
            transition: border-color 0.3s, background-color 0.3s;
        }

        .file-upload input[type="file"] {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload.drag-over {
            border-color: #007bff;
            background-color: #e9f5ff;
        }

        .file-upload img {
            max-width: 100%;
            max-height: 180px;
            display: none;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>
    <?php include 'offcanvas_sidebar.php'; ?>

    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Add Product</h2>

            <!-- Product Details -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Price ($)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Enter price" required>
                </div>
            </div>

            <!-- Description -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter product description" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stock_quantity" class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" placeholder="Enter stock quantity" required>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Men">Men</option>
                        <option value="Women">Women</option>
                        <option value="Unisex">Unisex</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="subcategory" class="form-label">SubCategory</label>
                    <select class="form-control" id="subcategory" name="subcategory" required>
                        <option value="">Select subcategory</option>
                        <option value="Discount">Discount</option>
                        <option value="Latest">Latest</option>
                        <option value="Popular">Popular</option>
                        <option value="Featured">Featured</option>
                    </select>
                </div>
            </div>

            <!-- Size Field -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="size" class="form-label">Size</label>
                    <input type="text" class="form-control" id="size" name="size" placeholder="Enter size (e.g., 100ml, 120ml)">
                </div>
            </div>

            <!-- Discount Field -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="discount_available" class="form-label">Discount Available</label>
                    <select class="form-select" id="discount_available" name="discount_available" onchange="toggleDiscountField()">
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3" id="discount_field" style="display: none;">
                    <label for="discount_percentage" class="form-label">Discount Percentage</label>
                    <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" placeholder="Enter discount percentage" oninput="calculateDiscount()">
                </div>
            </div>

            <!-- Discounted Price -->
            <div class="row" id="discounted_price_field" style="display: none;">
                <div class="col-md-12 mb-3">
                    <label for="discounted_price" class="form-label">Discounted Price ($)</label>
                    <input type="text" class="form-control" id="discounted_price" name="discounted_price" readonly>
                </div>
            </div>

            <!-- Image Uploads -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Main Image</label>
                    <div class="file-upload">
                        <input type="file" id="mainImage" name="image" accept="image/*" onchange="previewImage(event, 'mainImagePreview')" required>
                        <span>Click or drag to upload an image</span>
                        <img id="mainImagePreview" src="#" alt="Main Image Preview" style="display:none;">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Extra Image 1</label>
                    <div class="file-upload">
                        <input type="file" id="extraImage1" name="extra_image_1" accept="image/*" onchange="previewImage(event, 'extraImage1Preview')" required>
                        <span>Click or drag to upload an image</span>
                        <img id="extraImage1Preview" src="#" alt="Extra Image 1 Preview" style="display:none;">
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Extra Image 2</label>
                    <div class="file-upload">
                        <input type="file" id="extraImage2" name="extra_image_2" accept="image/*" onchange="previewImage(event, 'extraImage2Preview')" required>
                        <span>Click or drag to upload an image</span>
                        <img id="extraImage2Preview" src="#" alt="Extra Image 2 Preview" style="display:none;">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="add-button text-center">
                <button type="submit" class="btn btn-primary btn-lg">Add Product</button>
            </div>
        </div>
    </form><br>

    <script>
        function toggleDiscountField() {
            const discountField = document.getElementById('discount_field');
            const discountedPriceField = document.getElementById('discounted_price_field');
            const discountAvailable = document.getElementById('discount_available').value;

            if (discountAvailable === 'Yes') {
                discountField.style.display = 'block';
                discountedPriceField.style.display = 'block';
            } else {
                discountField.style.display = 'none';
                discountedPriceField.style.display = 'none';
            }
        }

        function calculateDiscount() {
            const price = parseFloat(document.getElementById('price').value);
            const discountPercentage = parseFloat(document.getElementById('discount_percentage').value);
            if (!isNaN(price) && !isNaN(discountPercentage)) {
                const discountedPrice = price - (price * (discountPercentage / 100));
                document.getElementById('discounted_price').value = discountedPrice.toFixed(2);
            }
        }

        function previewImage(event, previewId) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById(previewId);
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

</body>

</html>