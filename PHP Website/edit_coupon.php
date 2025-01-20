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

$coupon_id = $_GET['coupon_id'] ?? null;

if (!$coupon_id) {
    $_SESSION['error'] = 'Coupon ID is required.';
    header('Location: manage_coupon.php');
    exit;
}

// Fetch coupon details from the database
$query = "SELECT * FROM coupons WHERE coupon_id = $coupon_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = 'Coupon not found.';
    header('Location: manage_coupon.php');
    exit;
}

$coupon = mysqli_fetch_assoc($result);

// Handle Edit Coupon request
if (isset($_POST['update_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    $discount_percentage = $_POST['discount_percentage'];
    $valid_from = $_POST['valid_from'];
    $valid_to = $_POST['valid_to'];
    $minimum_purchase_amount = $_POST['minimum_purchase_amount'];

    // Update coupon in the database
    $query = "UPDATE coupons SET coupon_code = '$coupon_code', discount_percentage = '$discount_percentage', 
              valid_from = '$valid_from', valid_to = '$valid_to', minimum_purchase_amount = '$minimum_purchase_amount' 
              WHERE coupon_id = $coupon_id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Coupon updated successfully!';
        header('Location: manage_coupon.php');
        exit;
    } else {
        $_SESSION['error'] = 'Error updating coupon: ' . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coupon</title>
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9fafc;
            color: #333;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card {
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: #fff;
            overflow: hidden;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include 'admin_navbar.php'; ?>
<?php include 'offcanvas_sidebar.php'; ?>


    <!-- Main Content -->
    <div class="container mt-4">
        <h1 class="mb-4">Edit Coupon</h1>

        <!-- Display session messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Edit Coupon Form -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2 class="text-center">Edit Coupon Details</h2>
            </div>
            <div class="card-body">
                <form action="edit_coupon.php?coupon_id=<?php echo $coupon_id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="coupon_code" class="form-label">Coupon Code:</label>
                        <input type="text" class="form-control" name="coupon_code" value="<?php echo htmlspecialchars($coupon['coupon_code']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="discount_percentage" class="form-label">Discount Percentage:</label>
                        <input type="number" class="form-control" name="discount_percentage" value="<?php echo $coupon['discount_percentage']; ?>" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="valid_from" class="form-label">Valid From:</label>
                        <input type="date" class="form-control" name="valid_from" value="<?php echo $coupon['valid_from']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="valid_to" class="form-label">Valid To:</label>
                        <input type="date" class="form-control" name="valid_to" value="<?php echo $coupon['valid_to']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="minimum_purchase_amount" class="form-label">Minimum Purchase Amount:</label>
                        <input type="number" class="form-control" name="minimum_purchase_amount" value="<?php echo $coupon['minimum_purchase_amount']; ?>" step="0.01" required>
                    </div>

                    <button type="submit" class="btn btn-primary" name="update_coupon">Update Coupon</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
