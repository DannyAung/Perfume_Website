<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
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
// Check if an ID is passed in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $contact_id = $_GET['id'];

    // Prepare the DELETE SQL query
    $query = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Bind the contact ID to the query and execute
    mysqli_stmt_bind_param($stmt, 'i', $contact_id);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $_SESSION['success'] = "Contact message deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete the contact message. Please try again.";
    }

    // Close the prepared statement
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['error'] = "Invalid contact message ID.";
}

// Redirect back to the manage contact page
header('Location: manage_contact_us.php');
exit;

// Close the database connection
mysqli_close($conn);
?>
