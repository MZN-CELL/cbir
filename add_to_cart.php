<?php
session_start();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get product details from the form submission
$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$product_price = $_POST['product_price'];
$product_quantity = (int)$_POST['product_quantity'];
$product_image = strtolower($_POST['product_image']); // Ensure image filename is lowercase

// Extract HOG features
$hog_features = shell_exec("python extract_hog.py images/$product_image");
$hog_features = json_decode($hog_features); // Convert JSON output to array

// Check if the product already exists in the cart
$product_exists = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] === $product_id) {
        $item['quantity'] += $product_quantity; // Update quantity if the product is already in the cart
        $product_exists = true;
        break;
    }
}

if (!$product_exists) {
    // Add new product to the cart
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price,
        'quantity' => $product_quantity,
        'image' => $product_image,
        'hog_features' => $hog_features // Store HOG features
    ];
}

// Redirect back to the product page or cart
header("Location: cart.php");
exit();
?>
