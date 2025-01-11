<?php
// Start session
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

// Connect to the database
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Query to get total sales
$total_sales_query = "SELECT SUM(total_price) AS total_sales FROM orders";
$total_sales_result = mysqli_query($conn, $total_sales_query);
$total_sales_row = mysqli_fetch_assoc($total_sales_result);
$total_sales = $total_sales_row['total_sales'] ?? 0;

// Query to get order statistics for today
$order_stats_today_query = "SELECT 
                              COUNT(*) AS total_orders, 
                              SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
                              SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
                            FROM orders
                            WHERE DATE(created_at) = CURDATE()";
$order_stats_today_result = mysqli_query($conn, $order_stats_today_query);
$order_stats_today = mysqli_fetch_assoc($order_stats_today_result);
$total_orders_today = $order_stats_today['total_orders'];
$completed_orders_today = $order_stats_today['completed_orders'];
$cancelled_orders_today = $order_stats_today['cancelled_orders'];

// Query to get order statistics
$order_stats_query = "SELECT COUNT(*) AS total_orders, 
                              SUM(CASE WHEN oi.status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
                              SUM(CASE WHEN oi.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
                       FROM orders oi";
$order_stats_result = mysqli_query($conn, $order_stats_query);
$order_stats = mysqli_fetch_assoc($order_stats_result);
$total_orders = $order_stats['total_orders'];
$completed_orders = $order_stats['completed_orders'];
$cancelled_orders = $order_stats['cancelled_orders'];

// Query to get active users per month
$user_activity_query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS active_users 
                        FROM users 
                        WHERE created_at >= CURDATE() - INTERVAL 1 YEAR
                        GROUP BY month
                        ORDER BY month ASC";
$user_activity_result = mysqli_query($conn, $user_activity_query);

// Prepare arrays for chart data
$login_dates = [];
$active_users_data = [];
while ($row = mysqli_fetch_assoc($user_activity_result)) {
    $login_dates[] = $row['month'];
    $active_users_data[] = $row['active_users'];
}
$login_dates_json = json_encode($login_dates);
$active_users_json = json_encode($active_users_data);

// Query to get top 5 product performance
$product_performance_query = "SELECT p.product_id, p.product_name, SUM(ci.quantity) AS total_sold 
                              FROM cart_items ci
                              JOIN products p ON ci.product_id = p.product_id
                              GROUP BY p.product_id
                              ORDER BY total_sold DESC LIMIT 5";
$product_performance_result = mysqli_query($conn, $product_performance_query);

// Query to get coupon usage
$coupon_usage_query = "SELECT coupon_code, COUNT(*) AS usage_count 
                       FROM orders 
                       WHERE coupon_code IS NOT NULL 
                       GROUP BY coupon_code";
$coupon_usage_result = mysqli_query($conn, $coupon_usage_query);

// Query to get monthly sales
$monthly_sales_query = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, 
           SUM(total_price) AS total_sales
    FROM orders
    WHERE YEAR(created_at) = 2025
    GROUP BY month
    ORDER BY month ASC";
$monthly_sales_result = mysqli_query($conn, $monthly_sales_query);

$monthly_sales_months = [];
$monthly_sales_totals = [];
while ($row = mysqli_fetch_assoc($monthly_sales_result)) {
    $monthly_sales_months[] = $row['month'];
    $monthly_sales_totals[] = $row['total_sales'];
}
$monthly_sales_months_json = json_encode($monthly_sales_months);
$monthly_sales_totals_json = json_encode($monthly_sales_totals);
// Query: Top 5 products by sales
$product_performance_query = "SELECT p.product_name, SUM(ci.quantity) AS total_sold
                              FROM cart_items ci
                              JOIN products p ON ci.product_id = p.product_id
                              GROUP BY p.product_id
                              ORDER BY total_sold DESC
                              LIMIT 5";
$product_performance_result = mysqli_query($conn, $product_performance_query);

$top_products = [];
$top_sales = [];
while ($row = mysqli_fetch_assoc($product_performance_result)) {
    $top_products[] = $row['product_name'];
    $top_sales[] = $row['total_sold'];
}
$top_products_json = json_encode($top_products);
$top_sales_json = json_encode($top_sales);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-body {
            padding: 1.5rem;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9fafc;
            color: #333;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            margin-top: 50px;
        }

        .card-title {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-title i {
            margin-right: 10px;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.02);
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
    <div class="container my-5">
        <h1 class="text-center mb-4">Admin Reports Dashboard</h1>

        <div class="row g-4 mt-4">
            <!-- Total Sales by Month Line Chart -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Total Sales by Month (2025)</h5>
                        <canvas id="salesLineChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Active Users Bar Chart -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4"><i class="bi bi-person-circle"></i> Active Users in 2025</h5>
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                </div>
            </div>
        </div><br>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4"><i class="bi bi-gift"></i> Top 5 Products</h5>
                        <canvas id="topProductsDonutChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Order Statistics -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><i class="bi bi-calendar-day"></i> Order Statistics of Today</h5>
                        <p class="card-text mb-3">Total Orders: <?php echo $total_orders_today; ?></p>
                        <p class="card-text mb-3">Completed Orders: <?php echo $completed_orders_today; ?></p>
                        <p class="card-text mb-3">Cancelled Orders: <?php echo $cancelled_orders_today; ?></p>
                    </div>
                </div>
            </div>
            <!-- Coupon Usage -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><i class="bi bi-tag"></i> Coupon Usage</h5>
                        <ul class="list-group list-group-flush">
                            <?php while ($row = mysqli_fetch_assoc($coupon_usage_result)) { ?>
                                <li class="list-group-item"><?php echo $row['coupon_code']; ?> - Used: <?php echo $row['usage_count']; ?> times</li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Total Sales -->
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><i class="bi bi-cash"></i>All Total Sales</h5>
                        <p class="card-text mb-3">$<?php echo number_format($total_sales, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="row mt-4 border-top pt-3">
            <div class="col-md-6">
                <p class="text-muted">&copy; 2025 Fragrance Haven. All rights reserved.</p>
            </div>
    </footer>
    <!-- Chart.js Scripts -->
    <script>
        // Donut Chart for Top 5 Products
        var donutCtx = document.getElementById('topProductsDonutChart').getContext('2d');
        var topProductsDonutChart = new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo $top_products_json; ?>,
                datasets: [{
                    data: <?php echo $top_sales_json; ?>,
                    backgroundColor: [
                        'rgba(240, 42, 98, 0.41)',
                        'rgba(10, 108, 174, 0.2)',
                        'rgba(208, 237, 41, 0.75)',
                        'rgba(39, 17, 166, 0.79)',
                        'rgb(23, 116, 71)'
                    ],
                    borderColor: [
                        'rgb(233, 31, 75)',
                        'rgb(8, 119, 193)',
                        'rgb(208, 242, 71)',
                        'rgb(16, 11, 169)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        // Line Chart for Total Sales by Month
        var lineCtx = document.getElementById('salesLineChart').getContext('2d');
        var salesLineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: <?php echo $monthly_sales_months_json; ?>,
                datasets: [{
                    label: 'Total Sales ($)',
                    data: <?php echo $monthly_sales_totals_json; ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Sales ($)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Bar Chart for Active Users
        var ctx = document.getElementById('activeUsersChart').getContext('2d');
        var activeUsersChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $login_dates_json; ?>,
                datasets: [{
                    label: 'Active Users',
                    data: <?php echo $active_users_json; ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        },
                        ticks: {
                            maxRotation: 90,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Active Users'
                        }
                    }
                }
            }
        });
    </script>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </div>