<?php
require_once 'db_connection.php';
session_start();

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header('Location: manage_products.php');
    exit;
}

// Fetch product details to populate the form
$sql = "SELECT * FROM products WHERE product_id = :product_id";
$stmt = $conn->prepare($sql);
$stmt->execute([':product_id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['error'] = 'Product not found.';
    header('Location: manage_products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $product_name = htmlspecialchars($_POST['product_name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $category = htmlspecialchars($_POST['category']);
    $subcategory = htmlspecialchars($_POST['subcategory']);
    $size = htmlspecialchars($_POST['size'] ?? null); // Optional size
    $discount_available = $_POST['discount_available'];
    $discount_percentage = $_POST['discount_percentage'];
    $discounted_price = $_POST['discounted_price'];

    // Handle image uploads (same as in add_product.php)
    $upload_dir = 'products/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $upload_errors = [];



    function handle_image_upload($image_key, $upload_dir, $allowed_types, $existing_image = null)
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
                // Remove existing image if new one is uploaded
                if ($existing_image && file_exists($upload_dir . $existing_image)) {
                    unlink($upload_dir . $existing_image);
                }
                return ["path" => $file_name];  // Save relative file path
            } else {
                return ["error" => "Failed to upload $file_name."];
            }
        }
        return ["path" => $existing_image]; // Retain existing image if no new one is uploaded
    }

    // Handle images and store relative file paths
    $image_result = handle_image_upload('image', $upload_dir, $allowed_types, $product['image']);
    $extra_image_1_result = handle_image_upload('extra_image_1', $upload_dir, $allowed_types, $product['extra_image_1']);
    $extra_image_2_result = handle_image_upload('extra_image_2', $upload_dir, $allowed_types, $product['extra_image_2']);

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

    // Proceed with database update if no errors
    try {
        $sql = "UPDATE products 
                SET product_name = :product_name, 
                    image = :image, 
                    extra_image_1 = :extra_image_1, 
                    extra_image_2 = :extra_image_2, 
                    description = :description, 
                    price = :price, 
                    stock_quantity = :stock_quantity, 
                    category = :category, 
                    subcategory = :subcategory, 
                    size = :size, 
                    discount_available = :discount_available, 
                    discount_percentage = :discount_percentage, 
                    discounted_price = :discounted_price
                WHERE product_id = :product_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':product_name' => $product_name,
            ':image' => $image_result['path'],
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
            ':product_id' => $product_id,
        ]);

        $_SESSION['success'] = 'Product updated successfully!';
        header('Location: manage_products.php');
        exit;
    } catch (PDOException $e) {
        echo '<p class="text-danger">Error updating product: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- Bootstrap CSS -->
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
   
</head>
<style>
    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
        border: none;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<body>
<?php include 'admin_navbar.php'; ?>
<?php include 'offcanvas_sidebar.php'; ?>

    <form action="edit_products.php?id=<?= $product_id ?>" method="POST" enctype="multipart/form-data">
        <div class="container mt-5">
            <h2 class="text-center mb-4">Edit Product</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error'];
                                                unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?= $product['product_name'] ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="price" class="form-label">Price ($)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= $product['price'] ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?= $product['description'] ?></textarea>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="stock_quantity" class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?= $product['stock_quantity'] ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="Men" <?= $product['category'] == 'Men' ? 'selected' : '' ?>>Men</option>
                        <option value="Women" <?= $product['category'] == 'Women' ? 'selected' : '' ?>>Women</option>
                        <option value="Unisex" <?= $product['category'] == 'Unisex' ? 'selected' : '' ?>>Unisex</option>
                    </select>
                </div>
                <div class="col-md-6 mb-4">
                    <label for="subcategory" class="form-label">SubCategory</label>
                    <select class="form-control" id="subcategory" name="subcategory" required>
                        <option value="Discount" <?= $product['subcategory'] == 'Discount' ? 'selected' : '' ?>>Discount</option>
                        <option value="Latest" <?= $product['subcategory'] == 'Latest' ? 'selected' : '' ?>>Latest</option>
                        <option value="Popular" <?= $product['subcategory'] == 'Popular' ? 'selected' : '' ?>>Popular</option>
                        <option value="Featured" <?= $product['subcategory'] == 'Featured' ? 'selected' : '' ?>>Featured</option>
                    </select>
                </div>
            </div>

            <!-- Discount Section -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="discount_available" class="form-label">Discount Available</label>
                    <select class="form-control" id="discount_available" name="discount_available" required onchange="toggleDiscountField()">
                        <option value="No" <?= $product['discount_available'] == 'No' ? 'selected' : '' ?>>No</option>
                        <option value="Yes" <?= $product['discount_available'] == 'Yes' ? 'selected' : '' ?>>Yes</option>
                    </select>
                </div>

                <div class="col-md-6 mb-4" id="discount_field" style="<?= $product['discount_available'] == 'Yes' ? '' : 'display: none;' ?>">
                    <label for="discount_percentage" class="form-label">Discount Percentage</label>
                    <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" value="<?= $product['discount_percentage'] ?>" step="1" oninput="calculateDiscount()">
                </div>

                <div class="col-md-6 mb-4" id="discounted_price_field" style="<?= $product['discount_available'] == 'Yes' ? '' : 'display: none;' ?>">
                    <label for="discounted_price" class="form-label">Discounted Price ($)</label>
                    <input type="text" class="form-control" id="discounted_price" name="discounted_price" value="<?= $product['discounted_price'] ?>" readonly>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <label for="size" class="form-label">Size</label>
                <input type="text" class="form-control" id="size" name="size" value="<?= htmlspecialchars($product['size']); ?>" required>
            </div>

            <form action="edit_products.php?id=<?= $product_id ?>" method="POST" enctype="multipart/form-data">
                <div class="container mt-5">
                    <!-- Main Image Section -->
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Main Image</label>
                            <input type="file" id="mainImage" name="image" accept="image/*" onchange="previewImage(event, 'mainImagePreview')">
                            <!-- Show current image if exists -->
                            <?php if ($product['image']): ?>
                                <div>
                                    <img id="mainImagePreview" src="products/<?= $product['image'] ?>" alt="Main Image Preview" style="max-width: 100px; max-height: 100px;">
                                </div>
                            <?php else: ?>
                                <p>No image uploaded yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Extra Image 1 Section -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Extra Image 1</label>
                            <input type="file" id="extraImage1" name="extra_image_1" accept="image/*" onchange="previewImage(event, 'extraImage1Preview')">
                            <!-- Show current image if exists -->
                            <?php if ($product['extra_image_1']): ?>
                                <div>
                                    <img id="extraImage1Preview" src="products/<?= $product['extra_image_1'] ?>" alt="Extra Image 1 Preview" style="max-width: 100px; max-height: 100px;">
                                </div>
                            <?php else: ?>
                                <p>No extra image uploaded yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Extra Image 2 Section -->
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Extra Image 2</label>
                            <input type="file" id="extraImage2" name="extra_image_2" accept="image/*" onchange="previewImage(event, 'extraImage2Preview')">
                            <!-- Show current image if exists -->
                            <?php if ($product['extra_image_2']): ?>
                                <div>
                                    <img id="extraImage2Preview" src="products/<?= $product['extra_image_2'] ?>" alt="Extra Image 2 Preview" style="max-width: 100px; max-height: 100px;">
                                </div>
                            <?php else: ?>
                                <p>No extra image uploaded yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>


                    <div class="d-flex justify-content-between">
                        <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </div>
            </form>

         
            <!-- Bootstrap JS and Custom JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="edit_products.js"></script>
        </div>
</body>

</html>