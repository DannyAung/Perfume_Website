<?php
// Start the session to access session variables
session_start();

// Check if the user is logged in or has a session ID (use this for unique cart identification)
if (!isset($_SESSION['user_id'])) {
    die("User is not logged in.");
}

// Ensure we have the 'product_id' in the POST request
if (!isset($_POST['product_id'])) {
    die("Product ID not received in POST");
}

$product_id = $_POST['product_id'];

// Get the user-specific cart based on user_id (or another unique identifier)
$user_cart_key = 'cart_' . $_SESSION['user_id']; // Example: cart_123

// Debugging: Output the received product_id
echo "Received product_id: " . $product_id . "<br>";

// Check if the user's cart exists in the session
if (isset($_SESSION[$user_cart_key])) {
    // Debugging: Output the current cart contents
    echo "Before removal: <br>";
    echo "<pre>";
    var_dump($_SESSION[$user_cart_key]);
    echo "</pre>";

    // Flag to check if the item was found and removed
    $found = false;

    // Loop through the user's cart array and remove the item
    foreach ($_SESSION[$user_cart_key] as $key => $item) {
        // Debugging: Output current item details
        echo "Checking product ID: " . $item['product_id'] . "<br>";

        // Check if the product_id matches
        if ((int)$item['product_id'] === (int)$product_id) {
            // Product matches, remove it from the cart
            unset($_SESSION[$user_cart_key][$key]);

            // Re-index the array to avoid empty keys
            $_SESSION[$user_cart_key] = array_values($_SESSION[$user_cart_key]);

            // Set success message
            $_SESSION['success'] = 'Product removed from cart successfully!';
            $found = true;
            break;  // Exit the loop once the item is removed
        }
    }

    // If product was not found in cart
    if (!$found) {
        $_SESSION['error'] = 'Product with ID ' . $product_id . ' not found in the cart.';
    }

    // Debugging: Output updated cart after removal
    echo "After removal: <br>";
    echo "<pre>";
    var_dump($_SESSION[$user_cart_key]);
    echo "</pre>";

} else {
    $_SESSION['error'] = "No cart found for the current user.";
}

// Redirect to the cart page after removing the product
header('Location: add_to_cart.php');  // Redirect to the cart page
exit;
?>
