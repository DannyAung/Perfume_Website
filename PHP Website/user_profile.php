<?php
session_start();
require_once "db_connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
$user_id = (int)$_SESSION['user_id'];

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

$user_name = htmlspecialchars($user['user_name'] ?? 'User');
$email = htmlspecialchars($user['email'] ?? '');
$address = htmlspecialchars($user['address'] ?? 'Not added yet');
$phone_number = htmlspecialchars($user['phone_number'] ?? 'Not added yet');
$created_at = htmlspecialchars($user['created_at'] ?? '');
$user_image = !empty($user['user_image']) ? $user['user_image'] : 'default.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragrance Haven - User Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        .profile-wrapper {
            padding: 30px 0 60px;
        }

        .profile-card {
            margin-top: 20px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            background-color: #fff;
            overflow: hidden;
            border: none;
        }

        .profile-card .card-body {
            padding: 30px 20px;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .profile-info p {
            font-size: 1rem;
            margin-bottom: 12px;
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .profile-card .card-body {
                padding: 24px 16px;
            }

            .profile-image {
                width: 120px;
                height: 120px;
            }

            .profile-title {
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="py-3 bg-light">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="user_index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">User Profile</li>
            </ol>
        </div>
    </nav>

    <!-- Profile Content -->
    <div class="container profile-wrapper">
        <h1 class="mb-3">User Profile</h1>

        <div class="card profile-card">
            <div class="card-body text-center">
                <img src="uploads/profile_images/<?php echo htmlspecialchars($user_image); ?>"
                     alt="Profile Picture"
                     class="rounded-circle profile-image">

                <h3 class="card-title mt-3 profile-title">Welcome, <?php echo $user_name; ?>!</h3>

                <div class="profile-info mt-4">
                    <p><strong>Email:</strong> <?php echo $email; ?></p>
                    <p><strong>Address:</strong> <?php echo $address; ?></p>
                    <p><strong>Phone Number:</strong> <?php echo $phone_number; ?></p>
                    <p><strong>Member Since:</strong> <?php echo $created_at; ?></p>
                </div>

                <a href="edit_profile.php" class="btn btn-primary mt-3">Edit Profile</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>