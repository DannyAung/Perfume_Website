<?php
require_once "db_connection.php";

if (!isset($_SESSION)) {
    session_start(); // to create session if not exist
}

function ispasswordstrong($password)
{
    if (strlen($password) < 8) {
        return false;
    } elseif (isstrong($password)) {
        return true;
    } else {
        return false;
    }
}

function isstrong($password)
{
    $digitcount = 0;
    $capitalcount = 0;
    $speccount = 0;
    $lowercount = 0;
    foreach (str_split($password) as $char) {
        if (is_numeric($char)) {
            $digitcount++;
        } elseif (ctype_upper($char)) {
            $capitalcount++;
        } elseif (ctype_lower($char)) {
            $lowercount++;
        } elseif (ctype_punct($char)) {
            $speccount++;
        }
    }

    return ($digitcount >= 1 && $capitalcount >= 1 && $speccount >= 1);
}

if (isset($_POST['signup']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $terms = isset($_POST['terms']) ? true : false;

    if ($terms) {
        if ($password == $cpassword) {
            if (ispasswordstrong($password)) {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                try {
                    $sql = "INSERT INTO users (user_name, password, email) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $status = $stmt->execute([$name, $password_hash, $email]);
                    if ($status) {
                        $_SESSION['signupSuccess'] = 'Signup Success';
                        header("Location: user_login.php");
                        exit();
                    }
                } catch (PDOException $e) {
                    $password_err = "Error: " . $e->getMessage();
                }
            } else {
                $password_err = "Password must contain at least one digit, one capital letter, and one special character.";
            }
        } else {
            $password_err = "Passwords do not match.";
        }
    } else {
        $password_err = "You must agree to the terms and conditions.";
    }
}
// Check if user is logged in
$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .form-container {
            margin-bottom: 60px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .terms-label {
            font-size: 0.9rem;
        }

        .terms-text {
            font-size: 0.8rem;
            color: #777;
        }

        .form-container h4 {
            font-weight: bold;
            color: #007bff;
        }

        .btn {
            font-size: 1.1rem;
            padding: 12px 0;
        }

        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>

<body>

    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
            <div class="container-fluid">
                <!-- Logo and Brand -->
                <a class="navbar-brand d-flex align-items-center" href="user_index.php">
                    <img src="./images/perfume_logo.png" alt="Logo" style="width:50px; height:auto;">
                    <b class="ms-2 dm-serif-display-regular-italic custom-font-color">FRAGRANCE HAVEN</b>
                </a>

                <!-- Collapsible Content -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="d-flex flex-column flex-lg-row w-100 align-items-center">

                        <!-- Modern Search Bar in the Center -->
                        <div class="search-bar-container mx-auto my-2 my-lg-0">
                            <form method="GET" action="search.php" class="search-form mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control border-end-0 search-input" name="query" placeholder="Search for a product..." aria-label="Search" required>
                                    <button class="btn btn-primary search-btn border-start-0 rounded-end px-4 py-2 shadow-lg" type="submit">
                                        <i class="bi bi-search"></i> <!-- FontAwesome or Bootstrap Icons -->
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- Display Username or Guest -->
                        <span class="navbar-text me-3 my-2 my-lg-0">
                            Welcome, <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest'; ?>!
                        </span>

                        <!-- Account Dropdown for Logged-In Users -->
                        <?php if ($is_logged_in): ?>
                            <div class="dropdown me-3">
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
                    </div>
                </div>

                <!-- Login and Cart Buttons on the Right -->
                <div class="d-flex justify-content-center justify-content-lg-end my-2 my-lg-0">
                    <?php if (!$is_logged_in): ?>
                        <a href="user_login.php" class="btn login-btn me-3">Login/Register</a>
                    <?php endif; ?>
                    <a href="add_to_cart.php" class="btn cart-btn" id="cart-button">
                        <img src="./images/cart-icon.jpg" alt="Cart" style="width:20px; height:20px; margin-right:6px;">
                        Cart
                    </a>
                </div>
            </div>
        </nav>
       
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="col-md-6 col-sm-12 form-container">
                <h4 class="text-center mb-4">Sign Up</h4>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
                    <?php if (isset($password_err)) {
                        echo "<p class='alert alert-danger'>$password_err</p>";
                    } ?>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" name="cpassword" required>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="terms" id="terms" required>
                        <label class="form-check-label terms-label" for="terms">I agree to the <a href="terms.html" target="_blank">Terms and Conditions</a></label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" name="signup">Sign Up</button>
                </form>
                <p class="mt-3 text-center">Already a member? <a href="user_login.php">Login here</a>.</p>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>

</html>