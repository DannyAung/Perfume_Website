<?php
// Start session (optional, for feedback or error messages)
session_start();

// Database connection details
$host = 'localhost';
$username_db = 'root'; // Corrected variable name
$password_db = ''; // Corrected variable name
$db_name = 'ecom_website'; // Fixed inconsistent variable name
$port = 3306;

try {
    // Establish database connection
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4;port=$port", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['error'] = 'All fields are required!';
        header('Location: contact_us.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address!';
        header('Location: contact_us.php');
        exit;
    }

    try {
        // Save the data to the database
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message, created_at) VALUES (:name, :email, :message, NOW())");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message,
        ]);

        // Feedback for successful submission
        $_SESSION['success'] = 'Thank you for reaching out! We will get back to you shortly.';
        header('Location: contact_us.php');
        exit;
    } catch (PDOException $e) {
        // Handle database errors
        $_SESSION['error'] = 'An error occurred while saving your message. Please try again later.';
        header('Location: contact_us.php');
        exit;
    }
} else {
    // Redirect back to contact_us.php if accessed directly
    header('Location: contact_us.php');
    exit;
}
