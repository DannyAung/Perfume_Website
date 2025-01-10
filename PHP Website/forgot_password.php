<?php
require_once "db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    if (!empty($email)) {
        try {
            $sql = "SELECT user_id FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Generate a unique token
                $token = bin2hex(random_bytes(16));
                $expiry_time = date("Y-m-d H:i:s", strtotime("+1 hour"));

                // Store the token and expiry in the database (you need to add these fields in a "password_resets" table)
                $sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$user['user_id'], $token, $expiry_time]);

                // Send email to the user with the reset link
                $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";
                mail($email, "Password Reset", "Click this link to reset your password: $reset_link");

                echo "Password reset link sent to your email.";
            } else {
                echo "No account found with this email.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please enter your email.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="email">Enter your email to reset your password:</label>
        <input type="email" name="email" required>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
