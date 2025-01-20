<?php
// Database connection
$host = 'localhost';
$username_db = 'root';
$password_db = '';
$dbname = 'ecom_website';
$conn = mysqli_connect($host, $username_db, $password_db, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();
$user_id = $_SESSION['user_id']; // Ensure this is set

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Image Upload
    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['user_image']['name'];
        $image_tmp_name = $_FILES['user_image']['tmp_name'];
        $image_folder = "uploads/profile_images/";

        // Generate unique name
        $image_new_name = uniqid() . "_" . basename($image_name);

        // Move uploaded file
        if (move_uploaded_file($image_tmp_name, $image_folder . $image_new_name)) {
            // Update database
            $query = "UPDATE users SET user_image = ? WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $image_new_name, $user_id);

            if ($stmt->execute()) {
                echo "Profile updated successfully!";
            } else {
                echo "Error updating profile: " . $conn->error;
            }
        } else {
            echo "Error uploading file.";
        }
    }
}
?>
