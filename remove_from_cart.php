<?php
session_start();
$id = $_GET['id'] ?? '';

if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]); // Remove item from cart
}

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>
