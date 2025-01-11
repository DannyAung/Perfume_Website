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


// Fetch all reviews
$reviews_query = "SELECT r.review_id, r.user_id, r.product_id, r.review_text, r.rating, r.created_at, r.updated_at, 
                         u.user_name AS user_name, p.product_name AS product_name
                  FROM reviews r
                  LEFT JOIN users u ON r.user_id = u.user_id
                  LEFT JOIN products p ON r.product_id = p.product_id
                  ORDER BY r.created_at DESC";
$reviews_result = $conn->query($reviews_query);

// Handle review deletion
if (isset($_POST['delete_review'])) {
    $review_id = intval($_POST['review_id']);
    $delete_query = "DELETE FROM reviews WHERE review_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $review_id);

    if ($stmt->execute()) {
        $message = "Review deleted successfully.";
        header('Location: manage_reviews.php');
    } else {
        $message = "Failed to delete the review: " . $conn->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
        }
    
        h1 {
            font-size: 2.5rem;
            font-weight: 600;
        }
        .alert {
            font-weight: bold;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        .btn-danger {
            transition: background-color 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #dc3545;
            opacity: 0.8;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .actions-form button {
            font-size: 1rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="./images/perfume_logo.png" alt="Logo" style="width:50px;">
                ADMIN DASHBOARD
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_coupon.php">Coupons</a></li>
                    <li class="nav-item"><a class="nav-link" href="view_reports.php">Reports</a></li>
                </ul>
                <a href="logout.php" class="btn btn-outline-dark">Logout</a>
            </div>
        </div>
    </nav>

    <div class="header text-center">
        <h1>Manage Reviews</h1>
    </div>

    <div class="container mt-5">
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-warning">
                    <tr>
                        <th>Review ID</th>
                        <th>User</th>
                        <th>Product</th>
                        <th>Review Text</th>
                        <th>Rating</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reviews_result->num_rows > 0): ?>
                        <?php while ($review = $reviews_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['review_id']); ?></td>
                                <td><?php echo htmlspecialchars($review['user_name'] ?? 'Unknown User'); ?></td>
                                <td><?php echo htmlspecialchars($review['product_name'] ?? 'Unknown Product'); ?></td>
                                <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                                <td><?php echo htmlspecialchars($review['rating']); ?></td>
                                <td><?php echo htmlspecialchars($review['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($review['updated_at']); ?></td>
                                <td class="actions-form">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                                        <button type="submit" name="delete_review" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Are you sure you want to delete this review?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No reviews found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
