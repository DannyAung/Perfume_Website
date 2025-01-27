<?php

session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$search_query = '';
if (isset($_GET['search'])) {
    $search_query = htmlspecialchars($_GET['search']);
    $query = "SELECT * FROM shipping WHERE 
              shipping_method LIKE ? OR 
              shipping_fee LIKE ? OR 
              delivery_time LIKE ?";
    $stmt = $conn->prepare($query);
    $search_param = '%' . $search_query . '%';
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    // Fetch all
    $query = "SELECT * FROM shipping";
    $result = mysqli_query($conn, $query);
}


if (isset($_POST['add_shipping'])) {
    $shipping_method = $_POST['shipping_method'];
    $shipping_fee = $_POST['shipping_fee'];
    $delivery_time = $_POST['delivery_time'];

  
    $query = "INSERT INTO shipping (shipping_method, shipping_fee, delivery_time) 
              VALUES ('$shipping_method', '$shipping_fee', '$delivery_time')";
    if (mysqli_query($conn, $query)) {
        echo "<div class='alert alert-success'>New shipping method added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
    header("Location: manage_shipping.php");
    exit;
}


if (isset($_POST['update_shipping'])) {
    $shipping_id = $_POST['shipping_id'];
    $shipping_method = $_POST['shipping_method'];
    $shipping_fee = $_POST['shipping_fee'];
    $delivery_time = $_POST['delivery_time'];

 
    $query = "UPDATE shipping SET shipping_method='$shipping_method', 
                                  shipping_fee='$shipping_fee', 
                                  delivery_time='$delivery_time' 
              WHERE shipping_id='$shipping_id'";
    if (mysqli_query($conn, $query)) {
        echo "<div class='alert alert-success'>Shipping method updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
    header("Location: manage_shipping.php");
    exit;
}


if (isset($_GET['delete_shipping'])) {
    $shipping_id = $_GET['delete_shipping'];

 
    $query = "DELETE FROM shipping WHERE shipping_id='$shipping_id'";
    if (mysqli_query($conn, $query)) {
        echo "<div class='alert alert-success'>Shipping method deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
    header("Location: manage_shipping.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shipping Methods</title>
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

<body class="bg-light">
    <?php include 'admin_navbar.php'; ?>
    <?php include 'offcanvas_sidebar.php'; ?>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Manage Shipping Methods</h1>

        <form action="manage_shipping.php" method="GET" class="d-flex mb-4">
            <input class="form-control me-2" type="search" name="search" placeholder="Search shipping methods" aria-label="Search" value="<?= htmlspecialchars($search_query); ?>">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

     
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="mb-0">Add New Shipping Method</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="manage_shipping.php">
                    <div class="mb-3">
                        <label for="shipping_method" class="form-label">Shipping Method</label>
                        <input type="text" class="form-control" id="shipping_method" name="shipping_method" required>
                    </div>

                    <div class="mb-3">
                        <label for="shipping_fee" class="form-label">Shipping Fee</label>
                        <input type="number" class="form-control" id="shipping_fee" name="shipping_fee" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="delivery_time" class="form-label">Delivery Time</label>
                        <input type="text" class="form-control" id="delivery_time" name="delivery_time" required>
                    </div>

                    <button type="submit" name="add_shipping" class="btn btn-primary">Add Shipping Method</button>
                </form>
            </div>
        </div>

    
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Existing Shipping Methods</h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Shipping Method</th>
                            <th>Shipping Fee</th>
                            <th>Delivery Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td>" . $row['shipping_id'] . "</td>";
                                echo "<td>" . $row['shipping_method'] . "</td>";
                                echo "<td>" . $row['shipping_fee'] . "</td>";
                                echo "<td>" . $row['delivery_time'] . "</td>";
                                echo "<td>
                                        <a href='manage_shipping.php?edit_shipping=" . $row['shipping_id'] . "' class='btn btn-warning btn-sm'>Edit</a> 
                                        <a href='manage_shipping.php?delete_shipping=" . $row['shipping_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete?\")'>Delete</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>No shipping methods found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php

        if (isset($_GET['edit_shipping'])) {
            $shipping_id = $_GET['edit_shipping'];
            $edit_query = "SELECT * FROM shipping WHERE shipping_id='$shipping_id'";
            $edit_result = mysqli_query($conn, $edit_query);
            $edit_row = mysqli_fetch_assoc($edit_result);
        ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h2 class="mb-0">Edit Shipping Method</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="manage_shipping.php">
                        <input type="hidden" name="shipping_id" value="<?php echo $edit_row['shipping_id']; ?>">
                        <div class="mb-3">
                            <label for="shipping_method" class="form-label">Shipping Method</label>
                            <input type="text" class="form-control" id="shipping_method" name="shipping_method" value="<?php echo $edit_row['shipping_method']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="shipping_fee" class="form-label">Shipping Fee</label>
                            <input type="number" class="form-control" id="shipping_fee" name="shipping_fee" step="0.01" value="<?php echo $edit_row['shipping_fee']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="delivery_time" class="form-label">Delivery Time</label>
                            <input type="text" class="form-control" id="delivery_time" name="delivery_time" value="<?php echo $edit_row['delivery_time']; ?>" required>
                        </div>

                        <button type="submit" name="update_shipping" class="btn btn-success">Update Shipping Method</button>
                    </form>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>

</html>

<?php
mysqli_close($conn);
?>