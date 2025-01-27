<?php
session_start();


$host = 'localhost';
$username_db = 'root';
$password_db = ''; 
$db_name = 'ecom_website'; 

try {

    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4;port=$port", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve 
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);


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

        
        $_SESSION['success'] = 'Thank you for reaching out! We will get back to you shortly.';
        header('Location: contact_us.php');
        exit;
    } catch (PDOException $e) {
       
        $_SESSION['error'] = 'An error occurred while saving your message. Please try again later.';
        header('Location: contact_us.php');
        exit;
    }
} else {
    header('Location: contact_us.php');
    exit;
}
