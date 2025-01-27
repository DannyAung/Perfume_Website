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

// Check if edit_id is provided
if (!isset($_GET['edit_id']) || !is_numeric($_GET['edit_id'])) {
    header('Location: manage_payment.php');
    exit;
}

$payment_id = intval($_GET['edit_id']);
$message = "";

// Fetch the payment method details
$stmt = $conn->prepare("SELECT * FROM payment WHERE payment_id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header('Location: manage_payment.php');
    exit;
}

$payment = $result->fetch_assoc();
$stmt->close();

// Handle form submission for updating payment method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = trim($_POST['payment_method']);

    if (!empty($payment_method)) {
        $stmt = $conn->prepare("UPDATE payment SET payment_method = ?, updated_at = NOW() WHERE payment_id = ?");
        $stmt->bind_param("si", $payment_method, $payment_id);

        if ($stmt->execute()) {
            $message = "Payment method updated successfully.";
            $stmt->close();
            header("Location: manage_payment.php");
            exit;
        } else {
            $message = "Error updating payment method: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Payment method cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment Method</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Lilita+One&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<style>
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
<body>
    <?php include 'admin_navbar.php'; ?>
    <?php include 'offcanvas_sidebar.php'; ?>

    <div class="container mt-5">
        <h1 class="mb-4">Edit Payment Method</h1>

        <!-- Display Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"> <?= htmlspecialchars($message); ?> </div>
        <?php endif; ?>

        <!-- Edit Payment Method Form -->
        <div class="card">
            <div class="card-header">Edit Payment Method</div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <input type="text" class="form-control" id="payment_method" name="payment_method" value="<?= htmlspecialchars($payment['payment_method']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Payment Method</button>
                    <a href="manage_payment.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
