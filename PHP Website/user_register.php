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
body{
    background:#f4f7fc;
}

/* Main container */
.signup-container{
    max-width:900px;
    margin:40px auto;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
    background:white;
    overflow:hidden;
}

/* form section */
.form-container{
    padding:40px;
}

.form-container h4{
    font-weight:bold;
    color:#007bff;
}

/* inputs */
.form-control{
    margin-bottom:15px;
    border-radius:10px;
}

/* buttons */
.btn-signup{
    background:linear-gradient(45deg,#007bff,#0056b3);
    border:none;
    border-radius:10px;
    padding:12px;
}

/* image section */
.img-container{
    display:flex;
    justify-content:center;
    align-items:center;
    height:100%;
}

.img-container img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* tablet */
@media (max-width: 992px){

    .signup-container{
        margin:30px 15px;
    }

}

/* mobile */
@media (max-width:768px){

    .form-container{
        padding:25px;
    }

    .img-container{
        display:none;
    }
}

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