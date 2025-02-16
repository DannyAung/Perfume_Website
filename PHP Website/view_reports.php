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

//order statistics for today
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

//order statistics
$order_stats_query = "SELECT COUNT(*) AS total_orders, 
                              SUM(CASE WHEN oi.status = 'completed' THEN 1 ELSE 0 END) AS completed_orders,
                              SUM(CASE WHEN oi.status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled_orders
                       FROM orders oi";
$order_stats_result = mysqli_query($conn, $order_stats_query);
$order_stats = mysqli_fetch_assoc($order_stats_result);
$total_orders = $order_stats['total_orders'];
$completed_orders = $order_stats['completed_orders'];
$cancelled_orders = $order_stats['cancelled_orders'];

//active users per month
$user_activity_query = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS active_users 
                        FROM users 
                        WHERE created_at >= CURDATE() - INTERVAL 1 YEAR
                        GROUP BY month
                        ORDER BY month ASC";
$user_activity_result = mysqli_query($conn, $user_activity_query);



$login_dates = [];
$active_users_data = [];
while ($row = mysqli_fetch_assoc($user_activity_result)) {
    $login_dates[] = $row['month'];
    $active_users_data[] = $row['active_users'];
}
$login_dates_json = json_encode($login_dates);
$active_users_json = json_encode($active_users_data);

//top 5 product performance
$product_performance_query = "SELECT p.product_id, p.product_name, SUM(ci.quantity) AS total_sold 
                              FROM cart_items ci
                              JOIN products p ON ci.product_id = p.product_id
                              GROUP BY p.product_id
                              ORDER BY total_sold DESC LIMIT 5";
$product_performance_result = mysqli_query($conn, $product_performance_query);

//coupon usage
$coupon_usage_query = "SELECT coupon_code, COUNT(*) AS usage_count 
                       FROM orders 
                       WHERE coupon_code IS NOT NULL 
                       GROUP BY coupon_code";
$coupon_usage_result = mysqli_query($conn, $coupon_usage_query);

//top 10 loyal customers 
$loyal_customers_query = "
    SELECT u.user_name, COUNT(o.order_id) AS order_count 
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    GROUP BY u.user_id
    ORDER BY order_count DESC
    LIMIT 10";
$loyal_customers_result = mysqli_query($conn, $loyal_customers_query);


$loyal_customers_names = [];
$loyal_customers_orders = [];
while ($row = mysqli_fetch_assoc($loyal_customers_result)) {
    $loyal_customers_names[] = $row['user_name'];
    $loyal_customers_orders[] = $row['order_count'];
}
$loyal_customers_names_json = json_encode($loyal_customers_names);
$loyal_customers_orders_json = json_encode($loyal_customers_orders);

//monthly sales
$monthly_sales_query = "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, 
           SUM(total_price) AS total_sales
    FROM orders
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


//Top 5 products by sales===
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


//total sales by day for the current month
$daily_sales_query = "
    SELECT DATE(created_at) AS day, SUM(total_price) AS total_sales
    FROM orders
    WHERE YEAR(created_at) = 2025
    GROUP BY day
    ORDER BY day ASC";
$daily_sales_result = mysqli_query($conn, $daily_sales_query);

$daily_sales_dates = [];
$daily_sales_totals = [];
while ($row = mysqli_fetch_assoc($daily_sales_result)) {
    $daily_sales_dates[] = $row['day'];
    $daily_sales_totals[] = $row['total_sales'];
}
$daily_sales_dates_json = json_encode($daily_sales_dates);
$daily_sales_totals_json = json_encode($daily_sales_totals);


//total sales by year
$yearly_sales_query = "
    SELECT YEAR(created_at) AS year, SUM(total_price) AS total_sales
    FROM orders
    GROUP BY year
    ORDER BY year ASC";
$yearly_sales_result = mysqli_query($conn, $yearly_sales_query);

$yearly_sales_years = [];
$yearly_sales_totals = [];
while ($row = mysqli_fetch_assoc($yearly_sales_result)) {
    $yearly_sales_years[] = $row['year'];
    $yearly_sales_totals[] = $row['total_sales'];
}
$yearly_sales_years_json = json_encode($yearly_sales_years);
$yearly_sales_totals_json = json_encode($yearly_sales_totals);

// top payment method
$top_payment_method_query = "
    SELECT payment_method, COUNT(*) AS usage_count
    FROM orders
    GROUP BY payment_method
    ORDER BY usage_count DESC
    LIMIT 1";
$top_payment_method_result = mysqli_query($conn, $top_payment_method_query);
$top_payment_method = mysqli_fetch_assoc($top_payment_method_result)['payment_method'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <?php include 'admin_navbar.php'; ?>
    <?php include 'offcanvas_sidebar.php'; ?>

    <div class="container my-5">
        <h1 class="text-center mb-4">Admin Dashboards</h1>

        <div class="row g-4 mt-4">

            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><i class="bi bi-calendar-day"></i> Order Statistics of Today</h5>
                        <p class="card-text mb-3">Total Orders: <?php echo $total_orders_today; ?></p>
                        <p class="card-text mb-3">Completed Orders: <?php echo $completed_orders_today; ?></p>
                        <p class="card-text mb-3">Cancelled Orders: <?php echo $cancelled_orders_today; ?></p>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-2"><i class="bi bi-credit-card"></i> Top Payment Method</h5>
                        <p class="card-text mb-3">Payment Method: <?php echo $top_payment_method; ?></p>
                    </div>
                </div>
            </div>


            <div class="col-md-4">
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


            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4"><i class="bi bi-gift"></i> Top 5 Products</h5>
                        <div style="width: 60%; margin: auto;">
                            <canvas id="topProductsDonutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4"><i class="bi bi-person-circle"></i> Active Users</h5>
                        <canvas id="activeUsersChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Daily Total Sales</h5>
                        <canvas id="dailySalesLineChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Monthly Total Sales</h5>
                        <canvas id="salesLineChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">Yearly Total Sales</h5>
                        <canvas id="yearlySalesBarChart"></canvas>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4"><i class="bi bi-person-bounding-box"></i> Top 10 Loyal Customers</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Order Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($loyal_customers_names as $index => $name) { ?>
                                    <tr>
                                        <td><?php echo $name; ?></td>
                                        <td><?php echo $loyal_customers_orders[$index]; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        // Line Chart for Total Sales by Day
        var dailySalesCtx = document.getElementById('dailySalesLineChart').getContext('2d');
        var dailySalesLineChart = new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: <?php echo $daily_sales_dates_json; ?>,
                datasets: [{
                    label: 'Total Sales ($)',
                    data: <?php echo $daily_sales_totals_json; ?>,
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderColor: 'rgba(255, 159, 64, 1)',
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
                            text: 'Day'
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

        // Bar Chart for Total Sales by Year
        var yearlySalesCtx = document.getElementById('yearlySalesBarChart').getContext('2d');
        var yearlySalesBarChart = new Chart(yearlySalesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $yearly_sales_years_json; ?>,
                datasets: [{
                    label: 'Total Sales ($)',
                    data: <?php echo $yearly_sales_totals_json; ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Year'
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
    </script>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>