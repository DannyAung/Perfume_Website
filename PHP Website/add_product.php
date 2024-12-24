<?php
require_once "db_connection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $product_name = htmlspecialchars($_POST['product_name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $category = htmlspecialchars($_POST['category']);
    $subcategory = htmlspecialchars($_POST['subcategory'] ?? '');
    $size = htmlspecialchars($_POST['size'] ?? null); // Optional size
    $discount_available = $_POST['discount_available'];
    $discount_percentage = $_POST['discount_percentage'];
    $discounted_price = $_POST['discounted_price'];

    // Handle image uploads
    $upload_dir = 'products/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $upload_errors = [];

    function handle_image_upload($image_key, $upload_dir, $allowed_types)
    {
        if (isset($_FILES[$image_key]) && $_FILES[$image_key]['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES[$image_key]['tmp_name'];
            $file_name = preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES[$image_key]['name'])); // Sanitize file name
            $file_type = mime_content_type($file_tmp);

            // Validate file type
            if (!in_array($file_type, $allowed_types)) {
                return ["error" => "$file_name is not a valid image file."];
            }

            // Move file to upload directory
            $file_path = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $file_path)) {
                return ["path" => $file_name];  // Save relative file path
            } else {
                return ["error" => "Failed to upload $file_name."];
            }
        }
        return ["path" => null];
    }

    $image_result = handle_image_upload('image', $upload_dir, $allowed_types);
    $extra_image_1_result = handle_image_upload('extra_image_1', $upload_dir, $allowed_types);
    $extra_image_2_result = handle_image_upload('extra_image_2', $upload_dir, $allowed_types);

    // Check for upload errors
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

    // Proceed with database insertion if no errors
    try {
        $sql = "INSERT INTO products 
                (product_name, image, extra_image_1, extra_image_2, description, price, stock_quantity, category, subcategory, size, discount_available, discount_percentage, discounted_price) 
                VALUES (:product_name, :image, :extra_image_1, :extra_image_2, :description, :price, :stock_quantity, :category, :subcategory, :size, :discount_available, :discount_percentage, :discounted_price)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':product_name' => $product_name,
            ':image' => $image_result['path'], // Store relative path
            ':extra_image_1' => $extra_image_1_result['path'],
            ':extra_image_2' => $extra_image_2_result['path'],
            ':description' => $description,
            ':price' => $price,
            ':stock_quantity' => $stock_quantity,
            ':category' => $category,
            ':subcategory' => $subcategory,
            ':size' => $size,
            ':discount_available' => $discount_available,
            ':discount_percentage' => $discount_percentage,
            ':discounted_price' => $discounted_price,
        ]);

        $_SESSION['success'] = 'Product added successfully!';
        header('Location: manage_products.php');
        exit;
    } catch (PDOException $e) {
        echo '<p class="text-danger">Error adding product: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="./images/Logo.png" alt="Logo" style="width:50px;">
                <b>ADMIN DASHBOARD</b>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="view_reports.php">Reports</a></li>
                </ul>
                <a href="logout.php" class="btn btn-outline-dark">Logout</a>
            </div>
        </div>
    </nav>

    <form action="add_product.php" method="POST" enctype="multipart/form-data">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Add Product</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <!-- Product Name and Price -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>
            <div class="col-md-6 mb-4">
                <label for="price" class="form-label">Price ($)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
        </div>

        <!-- Description and Stock Quantity -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="col-md-6 mb-4">
                <label for="stock_quantity" class="form-label">Stock Quantity</label>
                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required>
            </div>
        </div>

        <!-- Category and Subcategory -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <label for="category" class="form-label">Category</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="">Select a category</option>
                    <option value="Men">Men</option>
                    <option value="Women">Women</option>
                    <option value="Unisex">Unisex</option>
                </select>
            </div>
            <div class="col-md-6 mb-4">
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

         <!-- Discount Available Section -->
<div class="row">
    <div class="col-md-6 mb-4">
        <label for="discount_available" class="form-label">Discount Available</label>
        <select class="form-control" id="discount_available" name="discount_available" required onchange="toggleDiscountField()">
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select>
    </div>

    <div class="col-md-6 mb-4" id="discount_field" style="display: none;">
        <label for="discount_percentage" class="form-label">Discount Percentage</label>
        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" step="1" oninput="calculateDiscount()">
    </div>

    <div class="col-md-6 mb-4" id="discounted_price_field" style="display: none;">
        <label for="discounted_price" class="form-label">Discount Price ($)</label>
        <input type="text" class="form-control" id="discounted_price" name="discounted_price" readonly>
    </div>
</div>

        <!-- Size Section -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <label for="size" class="form-label">Size</label>
                <input type="text" class="form-control" id="size" name="size" required>
            </div>
        </div>

       <div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label">Main Image</label>
        <div class="file-upload" style="border: 2px dashed #ccc; border-radius: 10px; width: 100%; height: 250px; display: flex; justify-content: center; align-items: center; overflow: hidden; position: relative; background-color: #f9f9f9;">
            <input type="file" id="mainImage" name="image" accept="image/*" onchange="previewImage(event, 'mainImagePreview')" style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;" required>
            <div class="upload-area" style="text-align: center;">
                <p style="margin: 0; font-size: 16px; color: #555;">Drop Your Image Here or <span style="color: blue; cursor: pointer;">Browse</span></p>
                <img id="mainImagePreview" src="#" alt="Main Image Preview" style="max-width: 100%; max-height: 100%; display: none; border-radius: 10px;">
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <label class="form-label">Extra Image 1</label>
        <div class="file-upload" style="border: 2px dashed #ccc; border-radius: 10px; width: 100%; height: 250px; display: flex; justify-content: center; align-items: center; overflow: hidden; position: relative; background-color: #f9f9f9;">
            <input type="file" id="extraImage1" name="extra_image_1" accept="image/*" onchange="previewImage(event, 'extraImage1Preview')" style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;">
            <div class="upload-area" style="text-align: center;">
                <p style="margin: 0; font-size: 16px; color: #555;">Drop Your Image Here or <span style="color: blue; cursor: pointer;">Browse</span></p>
                <img id="extraImage1Preview" src="#" alt="Extra Image 1 Preview" style="max-width: 100%; max-height: 100%; display: none; border-radius: 10px;">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <label class="form-label">Extra Image 2</label>
        <div class="file-upload" style="border: 2px dashed #ccc; border-radius: 10px; width: 100%; height: 250px; display: flex; justify-content: center; align-items: center; overflow: hidden; position: relative; background-color: #f9f9f9;">
            <input type="file" id="extraImage2" name="extra_image_2" accept="image/*" onchange="previewImage(event, 'extraImage2Preview')" style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;">
            <div class="upload-area" style="text-align: center;">
                <p style="margin: 0; font-size: 16px; color: #555;">Drop Your Image Here or <span style="color: blue; cursor: pointer;">Browse</span></p>
                <img id="extraImage2Preview" src="#" alt="Extra Image 2 Preview" style="max-width: 100%; max-height: 100%; display: none; border-radius: 10px;">
            </div>
        </div>
    </div>
</div>




 <!-- Submit Button -->
 <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary btn-lg py-2">Add Product</button>
        </div>
    </div>

<script>
 // Function to toggle discount fields based on discount availability
 function toggleDiscountField() {
        var discountField = document.getElementById('discount_field');
        var discountedPriceField = document.getElementById('discounted_price_field');
        var discountAvailable = document.getElementById('discount_available').value;

        if (discountAvailable === 'Yes') {
            discountField.style.display = 'block';
            discountedPriceField.style.display = 'block';
        } else {
            discountField.style.display = 'none';
            discountedPriceField.style.display = 'none';
        }
    }

    function calculateDiscount() {
        var price = parseFloat(document.getElementById('price').value);
        var discountPercentage = parseFloat(document.getElementById('discount_percentage').value);
        if (!isNaN(price) && !isNaN(discountPercentage)) {
            var discountedPrice = price - (price * (discountPercentage / 100));
            document.getElementById('discounted_price').value = discountedPrice.toFixed(2);
        }
    }

//File Upload
function setupDragAndDrop(uploadAreaId, fileInputId) {
    const uploadArea = document.getElementById(uploadAreaId);
    const fileInput = document.getElementById(fileInputId);

    // Highlight area when dragging over
    uploadArea.addEventListener("dragover", (event) => {
        event.preventDefault();
        uploadArea.classList.add("drag-over");
    });

    // Remove highlight when drag leaves
    uploadArea.addEventListener("dragleave", () => {
        uploadArea.classList.remove("drag-over");
    });

    // Handle file drop
    uploadArea.addEventListener("drop", (event) => {
        event.preventDefault();
        uploadArea.classList.remove("drag-over");
        const file = event.dataTransfer.files[0];
        if (file) {
            fileInput.files = event.dataTransfer.files; // Update input files
            previewImage(file, uploadArea);
        }
    });

    // Handle file selection via Browse button
    fileInput.addEventListener("change", () => {
        const file = fileInput.files[0];
        if (file) {
            previewImage(file, uploadArea);
        }
    });
}

// Function to preview the image before uploading
    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }


// Set up drag-and-drop for each file upload area
setupDragAndDrop("uploadMainArea", "mainImage");
setupDragAndDrop("uploadExtraArea1", "extraImage1");
setupDragAndDrop("uploadExtraArea2", "extraImage2");

</script>


       
</form>
</div>

</body>

</div>

</body>
</html>
