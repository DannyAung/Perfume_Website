<?php
session_start();  // Start session at the top

require_once "db_connection.php";

// Handle login logic
if (isset($_POST['login']) && $_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if email and password are provided
    if (!empty($email) && !empty($password)) {
        try {
            // Query to get user information
            $sql = "SELECT user_id, user_name, password FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            $info = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($info) {
                // Password verification
                $password_hash = $info['password'];
                if (password_verify($password, $password_hash)) {
                    // Success: Set session variables
                    $_SESSION['user_id'] = $info['user_id'];  // Ensure user_id is correctly set
                    $_SESSION['user_name'] = $info['user_name'];
                    $_SESSION['user_logged_in'] = true;  // Mark the user as logged in

                    // Redirect to product page
                    header("Location: user_index.php");
                    exit;
                } else {
                    $password_err = "Invalid password.";
                }
            } else {
                $password_err = "No account found with this email.";
            }
        } catch (PDOException $e) {
            $password_err = "Error: " . $e->getMessage();
        }
    } else {
        $password_err = "Please enter both email and password.";
    }
}

// if (isset($_POST['remember_me'])) {
//     setcookie("user_email", $email, time() + (30 * 24 * 60 * 60), "/"); // 30 days
//     setcookie("user_name", $info['user_name'], time() + (30 * 24 * 60 * 60), "/"); // Optional
// }
// $email = isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : '';
// $password = isset($_COOKIE['user_password']) ? $_COOKIE['user_password'] : '';

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
        /* Gradient Background */
        body {

            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        /* Container for Animation and Form */
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 90%;
            border-radius: 15px;
            max-width: 1000px;
            animation: fadeIn 0.5s ease-in-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
            /* Optional: Adds a subtle border */
        }

        /* Login Card Styling */
        .login-card {
            flex: 1;
            margin-left: 20px;
            animation: fadeIn 0.5s ease-in-out;
            backdrop-filter: blur(5px);
            /* Optional: Adds a subtle blur to the card itself */
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Animation Column */
        .animation-column {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px
        }


        /* Input Field Styling */
        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 12px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        /* Button Styling */
        .btn-login {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.3s, transform 0.2s;
        }

        .btn-login:hover {
            background: linear-gradient(45deg, #0056b3, #004494);
            transform: translateY(-2px);
        }

        /* Centered Text */
        .text-center {
            margin-bottom: 20px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 576px) {
            .login-container {
                flex-direction: column;
            }

            .login-card {
                margin-left: 0;
                width: 100%;
            }

            .animation-column {
                display: none;
                /* Hide animation on small screens */
            }
        }
    </style>
</head>

<body>
    <!-- Login Form Container -->
    <div class="login-container">
        <!-- Animation Column -->
        <div class="animation-column">
            <img src="images/login1.png" alt="Login Animation" style="width: 100%; height: auto;">
        </div>


        <div class="login-card">
            <h4 class="text-center text-primary mb-4">Login Form</h4>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">

                <?php if (isset($password_err)): ?>
                    <p class="alert alert-danger"><?php echo htmlspecialchars($password_err); ?></p>
                <?php endif; ?>


                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>


                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me">
                    <label class="form-check-label" for="remember_me">Remember Me</label>
                </div>
                <button type="submit" class="btn btn-login w-100" name="login">Login</button>
            </form>

            <p class="mt-3 ">
                <a href="forgot_password.php">Forgot your password</a>
            </p>

            <!-- Register Link -->
            <p class="mt-3 btn-signup">
                <a href="user_register.php">Create new account</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>