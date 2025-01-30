<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
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

if (isset($_GET['coupon_id'])) {
    $coupon_id = intval($_GET['coupon_id']);
    $query = "SELECT * FROM coupons WHERE coupon_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $coupon_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $coupon = $result->fetch_assoc();
    $stmt->close();
}

if (isset($_POST['edit_coupon'])) {
    $coupon_id = intval($_POST['coupon_id']);
    $coupon_code = htmlspecialchars($_POST['coupon_code']);
    $discount_percentage = htmlspecialchars($_POST['discount_percentage']);
    $valid_from = htmlspecialchars($_POST['valid_from']);
    $valid_to = htmlspecialchars($_POST['valid_to']);
    $minimum_purchase_amount = htmlspecialchars($_POST['minimum_purchase_amount']);

    // Handle Image Upload
    $coupon_image = $coupon['coupon_image']; // Keep the existing image by default
    if (isset($_FILES['coupon_image']) && $_FILES['coupon_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/coupons/";
        $coupon_image = $target_dir . basename($_FILES['coupon_image']['name']);
        if (!move_uploaded_file($_FILES['coupon_image']['tmp_name'], $coupon_image)) {
            $_SESSION['error'] = 'Failed to upload image.'; // Handle upload error
        }
    }

    // Update coupon in the database
    $query = "UPDATE coupons SET coupon_code = ?, discount_percentage = ?, valid_from = ?, valid_to = ?, minimum_purchase_amount = ?, coupon_image = ? WHERE coupon_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdssdsi", $coupon_code, $discount_percentage, $valid_from, $valid_to, $minimum_purchase_amount, $coupon_image, $coupon_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Coupon updated successfully!';
        header('Location: manage_coupon.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating coupon: ' . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coupon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9fafc;
            color: #333;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            padding: 20px;
            border: none;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

    </style>
</head>
<body>
    <?php include 'admin_navbar.php'; ?>
    <div class="container">
        <h2 class="text-center bg-primary text-white">Edit Coupon</h2>
        <div class="form-container">
            <form action="edit_coupon.php?coupon_id=<?php echo $coupon_id; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="coupon_id" value="<?php echo $coupon['coupon_id']; ?>">
                <div class="mb-3">
                    <label for="coupon_code" class="form-label">Coupon Code:</label>
                    <input type="text" name="coupon_code" class="form-control" value="<?php echo $coupon['coupon_code']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="discount_percentage" class="form-label">Discount Percentage:</label>
                    <input type="number" name="discount_percentage" class="form-control" step="0.01" value="<?php echo $coupon['discount_percentage']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="valid_from" class="form-label">Valid From:</label>
                    <input type="date" name="valid_from" class="form-control" value="<?php echo $coupon['valid_from']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="valid_to" class="form-label">Valid To:</label>
                    <input type="date" name="valid_to" class="form-control" value="<?php echo $coupon['valid_to']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="minimum_purchase_amount" class="form-label">Minimum Purchase Amount:</label>
                    <input type="number" name="minimum_purchase_amount" class="form-control" step="0.01" value="<?php echo $coupon['minimum_purchase_amount']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="coupon_image" class="form-label">Coupon Image:</label>
                    <input type="file" name="coupon_image" class="form-control" accept="image/*">
                    <?php if (!empty($coupon['coupon_image'])): ?>
                        <img src="<?php echo $coupon['coupon_image']; ?>" alt="Current Coupon Image" style="width: 100px; height: auto; margin-top: 10px;">
                    <?php endif; ?>
                </div>
                <button type="submit" name="edit_coupon" class="btn btn-success">Update Coupon</button>
            </form>
        </div>
    </div>
</body>
</html>