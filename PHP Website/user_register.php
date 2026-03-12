<?php
session_start();
require_once "db_connection.php";

function ispasswordstrong($password)
{
    if (strlen($password) < 8) {
        return false;
    }
    return isstrong($password);
}

function isstrong($password)
{
    $digitcount = 0;
    $capitalcount = 0;
    $speccount = 0;

    foreach (str_split($password) as $char) {
        if (is_numeric($char)) {
            $digitcount++;
        } elseif (ctype_upper($char)) {
            $capitalcount++;
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
    $terms = isset($_POST['terms']);

    if ($terms) {
        if ($password == $cpassword) {
            if (ispasswordstrong($password)) {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                $sql = "INSERT INTO users (user_name, password, email) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("sss", $name, $password_hash, $email);

                    if ($stmt->execute()) {
                        $_SESSION['signupSuccess'] = 'Signup Success';
                        header("Location: user_login.php");
                        exit();
                    } else {
                        $password_err = "Error: " . $stmt->error;
                    }

                    $stmt->close();
                } else {
                    $password_err = "Database error: " . $conn->error;
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

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fc;
            overflow-x: hidden;
        }

        .signup-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .signup-container {
            max-width: 1000px;
            width: 100%;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-container {
            padding: 40px 35px;
        }

        .form-container h4 {
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .form-control {
            margin-bottom: 15px;
            border-radius: 10px;
            min-height: 46px;
        }

        .btn-signup {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 1.05rem;
        }

        .btn-signup:hover {
            background: #0056b3;
            color: white;
        }

        .img-container {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
            min-height: 100%;
        }

        .img-container img {
            width: 100%;
            max-width: 420px;
            height: auto;
            object-fit: contain;
        }

        .form-check-label {
            font-size: 0.95rem;
        }

        .alert {
            font-size: 0.95rem;
            border-radius: 10px;
        }

        @media (max-width: 991.98px) {
            .signup-container {
                max-width: 700px;
            }

            .img-container {
                display: none !important;
            }

            .form-container {
                padding: 30px 22px;
            }
        }

        @media (max-width: 575.98px) {
            .signup-wrapper {
                padding: 15px 10px;
            }

            .signup-container {
                border-radius: 14px;
            }

            .form-container {
                padding: 24px 16px;
            }

            .form-container h4 {
                font-size: 1.8rem;
            }

            .btn-signup {
                font-size: 1rem;
                padding: 11px;
            }
        }
    </style>
</head>

<body>
    <div class="signup-wrapper">
        <div class="signup-container">
            <div class="row g-0">
                <div class="col-lg-6 col-12 form-container">
                    <h4 class="text-center">Sign Up</h4>

                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <?php if (isset($password_err)) : ?>
                            <p class="alert alert-danger"><?php echo htmlspecialchars($password_err); ?></p>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="cpassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="cpassword" id="cpassword" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="terms" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="terms.html" target="_blank">Terms and Conditions</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-signup w-100" name="signup">Sign Up</button>
                    </form>

                    <p class="mt-4 text-center">
                        Already a member? <a href="user_login.php">Login here</a>.
                    </p>
                </div>

                <div class="col-lg-6 img-container">
                    <img src="./images/register.jpg" alt="Register Image">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>