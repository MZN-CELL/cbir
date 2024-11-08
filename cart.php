<?php
session_start();
include 'includes/db_connect.php'; // Include the database connection

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cartItems = $_SESSION['cart'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Cart - BagStore</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Custom Styles -->
</head>
<body>
    <?php include 'includes/header.php'; ?> <!-- Include header -->

    <div class="container my-4">
        <h2 class="text-center">Your Cart</h2>
        <form method="POST" action="checkout.php"> <!-- Add action for checkout -->
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Produk</th>
                        <th scope="col">Harga Satuan</th>
                        <th scope="col">Kuantitas</th>
                        <th scope="col">Total Harga</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cartItems)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Keranjang Anda kosong.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cartItems as $key => $item): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        // Construct the image path
                                        $imagePath = "images/" . htmlspecialchars($item['image']);
                                        // Check if the image exists
                                        if (file_exists($imagePath)): ?>
                                            <img src="<?php echo $imagePath; ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;" class="me-2">
                                        <?php else: ?>
                                            <span class="text-danger">Image not found</span> <!-- Display error message if image is missing -->
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($item['name']); ?></span>
                                    </div>
                                </td>
                                <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td>
                                    <input type="number" name="quantity[<?php echo $key; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" style="width: 80px;">
                                </td>
                                <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="remove_from_cart.php?id=<?php echo $key; ?>" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="d-flex justify-content-between">
                <h4>Total: Rp 
                    <?php 
                    $total = array_sum(array_map(function($item) {
                        return $item['price'] * $item['quantity'];
                    }, $cartItems));
                    echo number_format($total, 0, ',', '.'); 
                    ?>
                </h4>
                <button type="submit" class="btn btn-success">Checkout</button>
            </div>
        </form>
    </div>

    <?php include 'includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>
