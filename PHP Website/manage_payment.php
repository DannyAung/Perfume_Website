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

// Handle form submission for adding a new payment method
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $payment_method = trim($_POST['payment_method']);
    if (!empty($payment_method)) {
        $stmt = $conn->prepare("INSERT INTO payment (payment_method) VALUES (?)");
        $stmt->bind_param("s", $payment_method);
        if ($stmt->execute()) {
            $message = "Payment method added successfully.";
        } else {
            $message = "Error adding payment method: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Payment method cannot be empty.";
    }
    header("Location: manage_payment.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $payment_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM payment WHERE payment_id = ?");
    $stmt->bind_param("i", $payment_id);
    if ($stmt->execute()) {
        $message = "Payment method deleted successfully.";
    } else {
        $message = "Error deleting payment method: " . $conn->error;
    }
    $stmt->close();
    header("Location: manage_payment.php");
    exit;
}

// Handle search request
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM payment WHERE payment_method LIKE ? ORDER BY created_at DESC");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM payment ORDER BY created_at ASC");
}
?>

<!DOCTYPE html>
<lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Manage Payment Methods</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
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


        <div class="container mt-5 ">
            <h1 class="mb-4 text-center">Manage Payment Methods</h1>

            <!-- Display Messages -->
            <?php if (isset($message)): ?>
                <div class="alert alert-info"><?= htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="mb-4">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search payment methods" value="<?= htmlspecialchars($search_query); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>

            <!-- Add Payment Method Form -->
            <div class="card mb-4">
                <div class="card-header">Add New Payment Method</div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <input type="text" class="form-control" id="payment_method" name="payment_method" placeholder="Enter payment method" required>
                        </div>
                        <button type="submit" name="add_payment" class="btn btn-primary">Add Payment Method</button>
                    </form>
                </div>
            </div>

            <!-- Payment Methods Table -->
            <div class="card">
                <div class="card-header">Existing Payment Methods</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Payment Method</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php $counter = 1; ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?=  htmlspecialchars($row['payment_id']); ?></td>
                                        <td><?= htmlspecialchars($row['payment_method']); ?></td>
                                        <td><?= htmlspecialchars($row['created_at']); ?></td>
                                        <td><?= htmlspecialchars($row['updated_at']); ?></td>
                                        <td>
                                            <!-- Edit functionality -->
                                            <a href="edit_payment.php?edit_id=<?= $row['payment_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="?delete_id=<?= $row['payment_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payment method?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No payment methods found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>