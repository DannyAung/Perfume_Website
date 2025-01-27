<?php

if (!isset($_SESSION)) {
    session_start();
}


$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$port = 3306;

$conn = mysqli_connect($host, $username_db, $password_db, $dbname, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

$user_id = $_SESSION['user_id']; 


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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</head>
<style>
    .card {
        margin-top: 30px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        background-color: #fff;
        overflow: hidden;
    }
</style>

<body>
<?php include 'navbar.php'; ?>

    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Profile</li>
            </ol>
        </div>

        <div class="container mt-3">
            <h1>User Profile</h1>

            <div class="card">
                <div class="card-body">
                  
                        <div class="card-body text-center">
                            <?php if (!empty($user['user_image'])): ?>
                                <img src="uploads/profile_images/<?php echo htmlspecialchars($user['user_image']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else: ?>
                                <img src="uploads/profile_images/default.png" alt="Default Profile Picture" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php endif; ?>
                            <h5 class="card-title mt-3">Welcome, <?php echo htmlspecialchars($user['user_name']); ?>!</h5>
                        </div>
                   

                    <p class="card-text">
                        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
                        <strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?><br>

                        <strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phone_number']); ?><br>
                        <strong>Member Since:</strong> <?php echo htmlspecialchars($user['created_at']); ?>
                    </p>
                    <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                   
                </div>
            </div>
        </div><br>
        <?php include 'footer.php'; ?>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>