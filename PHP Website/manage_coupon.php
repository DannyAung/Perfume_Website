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

// Handle Add Coupon request
if (isset($_POST['add_coupon'])) {
    $coupon_code = htmlspecialchars($_POST['coupon_code']);
    $discount_percentage = htmlspecialchars($_POST['discount_percentage']);
    $valid_from = htmlspecialchars($_POST['valid_from']);
    $valid_to = htmlspecialchars($_POST['valid_to']);
    $minimum_purchase_amount = htmlspecialchars($_POST['minimum_purchase_amount']);

    // Handle Image Upload
    $coupon_image = '';
    if (isset($_FILES['coupon_image']) && $_FILES['coupon_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/coupons/";
        $coupon_image = $target_dir . basename($_FILES['coupon_image']['name']);
        if (!move_uploaded_file($_FILES['coupon_image']['tmp_name'], $coupon_image)) {
            $_SESSION['error'] = 'Failed to upload image.'; // Handle upload error
        }
    }

    // Insert coupon into the database
    $query = "INSERT INTO coupons (coupon_code, discount_percentage, valid_from, valid_to, minimum_purchase_amount, coupon_image) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdssds", $coupon_code, $discount_percentage, $valid_from, $valid_to, $minimum_purchase_amount, $coupon_image);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Coupon added successfully!';
    } else {
        $_SESSION['error'] = 'Error adding coupon: ' . $conn->error;
    }
    $stmt->close();
}

// Handle Delete Coupon request
if (isset($_GET['delete_coupon'])) {
    $coupon_id = intval($_GET['delete_coupon']);

    // Delete coupon from database
    $query = "DELETE FROM coupons WHERE coupon_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $coupon_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Coupon deleted successfully!';
    } else {
        $_SESSION['error'] = 'Error deleting coupon: ' . $conn->error;
    }
    $stmt->close();
}

// Search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
    $query = "SELECT * FROM coupons WHERE 
              coupon_code LIKE ? OR 
              discount_percentage LIKE ? OR 
              valid_from LIKE ? OR 
              valid_to LIKE ? OR 
              minimum_purchase_amount LIKE ?";
    $stmt = $conn->prepare($query);
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("sssss", $search_param, $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $query = "SELECT * FROM coupons";
    $result = mysqli_query($conn, $query);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9fafc;
        }

        .card {
            margin-bottom: 20px;
        }

        .success-message,
        .error-message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <?php include 'admin_navbar.php'; ?>
    <?php include 'offcanvas_sidebar.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center">Manage Coupons</h1>

        <!-- Search Form -->
        <form action="manage_coupon.php" method="GET" class="d-flex mb-4">
            <input class="form-control me-2" type="search" name="search" placeholder="Search coupons" value="<?= htmlspecialchars($search_query); ?>">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <!-- Display session messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message"><?php echo $_SESSION['message'];
                                            unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message"><?php echo $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Coupons List -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">Existing Coupons</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Coupon Code</th>
                            <th>Discount (%)</th>
                            <th>Valid From</th>
                            <th>Valid To</th>
                            <th>Minimum Purchase</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($coupon = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($coupon['coupon_image'])): ?>

                                        <img src="<?php echo 'uploads/coupons/' . htmlspecialchars(basename($coupon['coupon_image'])); ?>"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="bi bi-image text-muted"></i>
                                    <?php endif; ?>
                                </td>


                                <td><?php echo htmlspecialchars($coupon['coupon_code']); ?></td>
                                <td><?php echo $coupon['discount_percentage']; ?>%</td>
                                <td><?php echo $coupon['valid_from']; ?></td>
                                <td><?php echo $coupon['valid_to']; ?></td>
                                <td>$<?php echo number_format($coupon['minimum_purchase_amount'], 2); ?></td>
                                <td>
                                    <a href="edit_coupon.php?coupon_id=<?php echo $coupon['coupon_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="manage_coupon.php?delete_coupon=<?php echo $coupon['coupon_id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this coupon?')" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

      
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Add New Coupon</h4>
            </div>
            <div class="card-body">
                <form action="manage_coupon.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="coupon_code" class="form-label">Coupon Code:</label>
                        <input type="text" name="coupon_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="discount_percentage" class="form-label">Discount Percentage:</label>
                        <input type="number" name="discount_percentage" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="valid_from" class="form-label">Valid From:</label>
                        <input type="date" name="valid_from" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="valid_to" class="form-label">Valid To:</label>
                        <input type="date" name="valid_to" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="minimum_purchase_amount" class="form-label">Minimum Purchase Amount:</label>
                        <input type="number" name="minimum_purchase_amount" class="form-control" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="coupon_image" class="form-label">Coupon Image:</label>
                        <input type="file" name="coupon_image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="add_coupon" class="btn btn-success">Add Coupon</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>