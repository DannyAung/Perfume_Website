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
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
       
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        } 
    </style>
</head>

 <body>
     <!-- Navbar -->
 <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="./images/Logo.png" alt="Logo" style="width:50px; height:auto;">
            <b class="ms-2 dm-serif-display-regular-italic custom-font-color">FRAGRANCE  HAVEN</b>
        </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex w-100 align-items-center">
        <!-- Center the Search Bar -->
                <div class="mx-auto">
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
                
        <!-- Login and Cart Buttons on the Right -->
        <div class="LoginCart">
    <a href="user_login.php" class="btn login-btn">Login/Register</a>
    <a href="cart.php" class="btn cart-btn" id="cart-button">
        <img src="./images/cart-icon.jpg" alt="Cart" style="width:20px; margin-right:6px;">
        Cart 
    </a>
    </div>
         </div>
             </div>
</nav>

    <!-- New Navigation Links Section -->
    <div class="py-1">
        <div class="container">
            <ul class="nav justify-content">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">
                    Category
                </a>
                <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                    <li><a class="dropdown-item" href="#">Men</a></li>
                    <li><a class="dropdown-item" href="#">Women</a></li>
                    <li><a class="dropdown-item" href="#">Unisex</a></li>
                </ul>
            </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Delivery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="col-md-6 col-sm-12 form-container">
            <h4 class="text-center text-primary mb-4">Sign Up</h4>
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
                <button type="submit" class="btn btn-primary w-100" name="signup">Sign Up</button>
            </form>
            <p class="mt-3 text-center">Already a member? <a href="user_login.php">Login here</a>.</p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>