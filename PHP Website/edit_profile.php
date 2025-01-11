<?php
// Start session
if (!isset($_SESSION)) {
    session_start();
}

// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Fetch user details
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $address = $_POST['address']; // New address field
    $phone_number = $_POST['phone_number']; // New phone_number field
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate current password
    $query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
        $stored_password_hash = $user_data['password'];

        if (password_verify($password, $stored_password_hash)) {
            // Current password is correct

            // Handle new password validation
            if (!empty($new_password)) {
                if ($new_password === $confirm_password) {
                    $password = password_hash($new_password, PASSWORD_DEFAULT);
                } else {
                    $_SESSION['error'] = "New password and confirm password do not match.";
                    header("Location: edit_profile.php");
                    exit;
                }
            } else {
                // If no new password, keep the old password hash
                $password = $stored_password_hash;
            }

            // Update user details in the database
            $update_query = "UPDATE users SET user_name = ?, email = ?, password = ?, address = ?, phone_number = ? WHERE user_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssssi", $user_name, $email, $password, $address, $phone_number, $user_id);

            if ($stmt->execute()) {
                // Update session variables
                $_SESSION['user_name'] = $user_name;

                // Set success message
                $_SESSION['success'] = "Profile updated successfully.";
                header("Location: user_profile.php");
                exit;
            } else {
                $_SESSION['error'] = "Failed to update profile.";
            }
        } else {
            // Incorrect current password
            $_SESSION['error'] = "Incorrect current password.";
            header("Location: edit_profile.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header("Location: edit_profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragrance Haven</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<style>
    .card {
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #007bff;
        color: white;
        text-align: center;

    }

    .form-label {
        font-weight: 500;
    }

    .alert {
        margin-top: 1rem;
    }

    .mb-4 {
        text-align: left;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
        <div class="container-fluid">
            <!-- Logo and Brand -->
            <a class="navbar-brand d-flex align-items-center" href="user_index.php">
                <img src="./images/perfume_logo.png" alt="Logo" style="width:50px; height:auto;">
                <b class="ms-2" style="font-family: 'Roboto', sans-serif; font-weight: 300; color: #333;">FRAGRANCE HAVEN</b>
            </a>

            <!-- Toggler for Small Screens -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="d-flex flex-column flex-lg-row w-100 align-items-center">

                    <!-- Modern Search Bar in the Center -->
                    <div class="search-bar-container mx-lg-auto my- my-lg-0 w-100 w-lg-auto">
                        <form method="GET" action="search.php" class="search-form d-flex">
                            <input type="text" class="form-control border-end-0 search-input" name="query" placeholder="Search for a product..." aria-label="Search" required>
                            <button class="btn btn-primary search-btn border-start-1 rounded-end-2 px-4  shadow-lg" type="submit">
                                <i class="bi bi-search"></i> <!-- FontAwesome or Bootstrap Icons -->
                            </button>
                        </form>
                    </div>

                    <!-- Display Username or Guest -->
                    <span class="navbar-text mx-lg-3 my-2 my-lg-0 text-center">
                        Welcome, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>!
                    </span>

                    <!-- Account Dropdown for Logged-In Users -->
                    <?php if ($is_logged_in): ?>
                        <div class="dropdown mx-lg-3 my-2 my-lg-0">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Account
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                                <li><a class="dropdown-item" href="user_orders.php">Orders</a></li>
                                <li><a class="dropdown-item" href="user_profile.php">View Profile</a></li>
                                <li><a class="dropdown-item" href="user_logout.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php endif; ?>


                    <!-- Login and Cart Buttons -->
                    <div class="d-flex justify-content-center justify-content-lg-end my-2 my-lg-0">
                        <?php if (!$is_logged_in): ?>
                            <a href="user_login.php" class="btn login-btn me-3 ">Login/Register</a>
                        <?php endif; ?>
                        <!-- Favorite Link -->
                        <a class="nav-link d-flex align-items-center justify-content-center mx-lg-3 my-2 my-lg-0" href="favorite.php">
                            <i class="bi bi-heart fs-5"></i> <!-- Larger Icon -->
                        </a>
                        <a href="add_to_cart.php" class="btn cart-btn" id="cart-button">
                            <img src="./images/cart-icon.jpg" alt="Cart" style="width:24px; height:24px; margin-right:2px;">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item" onclick="history.back()">Back</li>
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
            </ol>
        </div>


        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="mb-0">Edit Profile</h4>
                        </div>
                        <div class="card-body">
                            <!-- Display error or success messages -->
                            <?php if (isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                                                unset($_SESSION['error']); ?></div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success"><?php echo $_SESSION['success'];
                                                                    unset($_SESSION['success']); ?></div>
                            <?php endif; ?>

                            <form action="edit_profile.php" method="POST">
                                <!-- User Name -->
                                <div class="mb-4">
                                    <label for="user_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>" required>
                                </div>

                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>

                                <!-- Address -->
                                <div class="mb-4">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                                </div>

                                <!-- Phone Number -->
                                <div class="mb-4">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                                </div>

                                <!-- Current Password -->
                                <div class="mb-4">
                                    <label for="password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>

                                <!-- New Password -->
                                <div class="mb-4">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>

                                <!-- Confirm New Password -->
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>

                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="bg-dark text-white py-5">
            <div class="container">
                <div class="row">

                    <div class="col-md-4 mb-4">
                        <h5 class="mb-3">About Us</h5>
                        <p class="text-muted">Fragrance Haven is your ultimate destination for high-quality perfumes that elevate your senses. Explore our wide range of fragrances designed to suit every occasion and personality.</p>
                    </div>

                    <div class="col-md-2 mb-4">
                        <h5 class="mb-1">Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="user_index.php" class="text-white text-decoration-none">Home</a></li>
                            <li><a href="women_category.php" class="text-white text-decoration-none">Women’s Collection</a></li>
                            <li><a href="men_category.php" class="text-white text-decoration-none">Men’s Collection</a></li>
                            <li><a href="unisex_category.php" class="text-white text-decoration-none">Unisex Collection</a></li>
                            <li><a href="about_us.php" class="text-white text-decoration-none">About Us</a></li>
                            <li><a href="contact_us.php" class="text-white text-decoration-none">Contact Us</a></li>
                        </ul>
                    </div>

                    <div class="col-md-2 mb-4">
                        <h5 class="mb-1">Customer Care</h5>
                        <ul class="list-unstyled">
                            <li><a href="privacy_policy.php" class="text-white text-decoration-none">Privacy Policy</a></li>
                            <li><a href="term_and_conditions.php" class="text-white text-decoration-none">Terms and Conditions</a></li>
                        </ul>
                    </div>

                    <div class="col-md-3 mb-4">
                        <h5 class="mb-4">Contact Info</h5>
                        <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> Pyi Yeik Thar Street, Kamayut, Yangon, Myanmar</p>
                        <p class="text-muted"><i class="fas fa-phone-alt me-2"></i> +959450197415</p>
                        <p class="text-muted"><i class="fas fa-envelope me-2"></i> support@fragrancehaven.com</p>
                    </div>
                </div>

                <div class="row mt-4 border-top pt-3">
                    <div class="col-md-6">
                        <p class="text-muted">&copy; 2025 Fragrance Haven. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="https://www.instagram.com/" class="text-white me-3 text-decoration-none" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="https://www.facebook.com/" class="text-white me-3 text-decoration-none" target="_blank"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="https://twitter.com/" class="text-white text-decoration-none" target="_blank"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>
        </footer>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>