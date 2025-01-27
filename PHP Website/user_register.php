<?php
require_once "db_connection.php";

if (!isset($_SESSION)) {
    session_start();
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

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .container {
            display: flex;
            height: 85vh;
            margin-top: 47px;
            margin-left: 335px;
            border-radius: 20px;
            max-width: 900px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: background 0.3s, transform 0.2s;
        }

        .row {
            align-items: center;
        }


        .form-container {
            padding: 30px;
        }

        .form-container h4 {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }


        .form-control {
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05);
        }


        .terms-label {
            font-size: 0.9rem;
            color: #007bff;
        }

        .terms-check {
            font-size: 0.8rem;
            color: #777;
        }


        .btn {
            font-size: 1.1rem;
            padding: 6px 0;
            border-radius: 10px;
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            transition: background 0.3s, transform 0.2s;
        }

        .btn-signup {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 10px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }


        .alert {
            font-size: 0.9rem;
            margin-bottom: 20px;
        }


        .img-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            height: 100%;

        }

        .img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }

            .img-container {
                display: none;
            }

            .btn {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center ">
        <div class="row w-100">

            <div class="col-md-6 col-sm-10 form-container">
                <h4 class="text-center mb-1">Sign Up</h4>
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


                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="terms" id="terms" required>
                        <label class="form-check-label terms-label" for="terms">I agree to the <a href="terms.html" target="_blank">Terms and Conditions</a></label>
                    </div>


                    <button type="submit" class="btn btn-signup w-100" name="signup">Sign Up</button>
                </form>
                <p class="mt-3 text-center">Already a member? <a href="user_login.php">Login here</a>.</p>
            </div>

            <div class="col-md-6 d-none d-md-flex img-container">
                <img src="./images/register.jpg" alt="Register GIF">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>