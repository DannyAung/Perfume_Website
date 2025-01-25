<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecom_website";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$user_id = $_POST['user_id'];
$product_id = $_POST['product_id'];
$review_text = trim($_POST['review_text']);
$rating = (int)$_POST['rating'];
$created_at = date('Y-m-d H:i:s');

// Validate inputs
if (empty($review_text) || $rating < 1 || $rating > 5) {
    echo "Invalid review or rating. Please try again.";
    exit;
}


$insert_query = "INSERT INTO reviews (user_id, product_id, review_text, rating, created_at) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_query);
$stmt->bind_param("iisis", $user_id, $product_id, $review_text, $rating, $created_at);

if ($stmt->execute()) {
    echo "Review submitted successfully!";
} else {
    echo "Failed to submit review. Please try again.";
}


header("Location: user_orders.php");
exit;
?>
