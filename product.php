<?php
session_start();
include 'includes/db_connect.php';
include 'includes/header.php';

$id = $_GET['id'];
$sql = "SELECT * FROM bags WHERE id = $id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded shadow" alt="Bag Image">
        </div>
        <div class="col-md-6">
            <h2 class="text-orange"><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <h4 class="text-orange">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></h4>
            <p><strong>Stock:</strong> <?php echo $product['stock']; ?> available</p>
            
            <!-- Add to Cart Form -->
            <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                <div class="input-group mb-3">
                    <input type="number" name="product_quantity" class="form-control" placeholder="Quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
