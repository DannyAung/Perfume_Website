
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

$sql = "
    SELECT o.order_id, o.user_id, o.total_price, o.name AS customer_name, o.address, o.phone, o.email, 
        o.payment_method, o.status, o.created_at, u.user_name, 
        oi.product_id, p.product_name, p.image, oi.quantity
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    ORDER BY o.order_id ASC
";

$result = mysqli_query($conn, $sql);

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    // Update order status
    $update_sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $order_id);

    if ($update_stmt->execute()) {
        // If order is cancelled, update the product quantities
        if ($new_status == 'cancelled') {
            // Get the products in the cancelled order
            $order_items_sql = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
            $order_items_stmt = $conn->prepare($order_items_sql);
            $order_items_stmt->bind_param("i", $order_id);
            $order_items_stmt->execute();
            $order_items_result = $order_items_stmt->get_result();

            // Loop through each product and update the quantity
            while ($item = $order_items_result->fetch_assoc()) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];

                // Increase the product quantity in the inventory
                $update_product_sql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?";
                $update_product_stmt = $conn->prepare($update_product_sql);
                $update_product_stmt->bind_param("ii", $quantity, $product_id);
                $update_product_stmt->execute();
            }
        }
        // After the update, reload the page to reflect the new status
        header("Location: manage_orders.php");
        exit;
    } else {
        echo "Error updating order status: " . $update_stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
        }
        .container {
            margin-top: 50px;
        }
        .order-table {
            margin-top: 30px;
        }
        .order-table th, .order-table td {
            vertical-align: middle;
        }
        .status-dropdown {
            width: 150px;
        }
        .product-list {
            list-style-type: none;
            padding-left: 0;
        }
        .product-list li {
            margin-bottom: 10px;
        }
        .product-image {
            width: 50px;
            height: auto;
        }
    </style>
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
            <a href="admin_login.php" class="btn btn-outline-dark">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="text-center">Manage Orders</h1>

    <table class="table table-striped order-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Address</th>
                <th>Status</th>
                <th>Products</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                $orders = [];
                while ($order = mysqli_fetch_assoc($result)) {
                    $orders[$order['order_id']]['order_id'] = $order['order_id'];
                    $orders[$order['order_id']]['customer_name'] = $order['customer_name'];
                    $orders[$order['order_id']]['total_price'] = $order['total_price'];
                    $orders[$order['order_id']]['payment_method'] = $order['payment_method'];
                    $orders[$order['order_id']]['address'] = $order['address'];
                    $orders[$order['order_id']]['status'] = $order['status'];
                    $orders[$order['order_id']]['products'][] = [
                        'product_name' => $order['product_name'],
                        'image' => $order['image'],
                        'quantity' => $order['quantity']
                    ];
                }

                foreach ($orders as $order) {
                    echo "<tr>
                            <td>" . $order['order_id'] . "</td>
                            <td>" . htmlspecialchars($order['customer_name']) . "</td>
                            <td>$" . number_format($order['total_price'], 2) . "</td>
                            <td>" . htmlspecialchars($order['payment_method']) . "</td>
                            <td>" . htmlspecialchars($order['address']) . "</td>
                            <td>" . htmlspecialchars($order['status']) . "</td>
                            <td>
                                <ul class='product-list'>";
                    foreach ($order['products'] as $product) {
                        // Constructing the image URL
                        $image_url = 'products/' . htmlspecialchars($product['image']);
                        echo "<li>
                                <img src='" . $image_url . "' alt='" . htmlspecialchars($product['product_name']) . "' class='product-image'>
                                " . htmlspecialchars($product['product_name']) . "  "." x " . $product['quantity'] . "
                              </li>";
                    }
                    echo "      </ul>
                            </td>
            <td>
                <form method='post' action='manage_orders.php' class='d-inline'>
                    <input type='hidden' name='order_id' value='" . $order['order_id'] . "'>
                    <select name='status' class='form-control status-dropdown'>
                        <option value='pending' " . ($order['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                        <option value='completed' " . ($order['status'] == 'completed' ? 'selected' : '') . ">Completed</option>
                        <option value='shipped' " . ($order['status'] == 'shipped' ? 'selected' : '') . ">Shipped</option>
                        <option value='cancelled' " . ($order['status'] == 'cancelled' ? 'selected' : '') . ">Cancelled</option>
                        <option value='delivered' " . ($order['status'] == 'delivered' ? 'selected' : '') . ">Delivered</option>
                    </select>
                    <button type='submit' name='update_status' class='btn btn-primary btn-sm mt-2'>Update</button>
                </form>
            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$conn->close();
?>
