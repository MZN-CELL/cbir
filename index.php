<?php
session_start();
include 'includes/db_connect.php'; // Include the database connection

// Get the selected category from the query string
$selected_category = $_GET['category'] ?? '';

// Fetch products based on the selected category
$sql = "SELECT * FROM bags";
if ($selected_category) {
    $sql .= " WHERE category = '$selected_category'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Women’s Bags - Shopee Style</title>
    <link rel="stylesheet" href="styles.css"> <!-- Custom Styles -->
</head>
<body>
    <?php include 'includes/header.php'; ?> <!-- Include header -->

    <nav class="bg-orange text-white py-2">
    <div class="container">
        <div class="d-flex justify-content-center">
            <a href="index.php" class="btn btn-outline-light me-3">All</a>
            <a href="index.php?category=Tote Bag" class="btn btn-outline-light me-3">Tote Bags</a>
            <a href="index.php?category=Handbag" class="btn btn-outline-light me-3">Handbags</a>
            <a href="index.php?category=Shoulder Bag" class="btn btn-outline-light me-3">Shoulder Bags</a>
            <a href="index.php?category=Clutch" class="btn btn-outline-light me-3">Clutches</a>
            <a href="index.php?category=Purse Bag" class="btn btn-outline-light">Purse Bags</a>
        </div>
    </div>
</nav>



    <main class="container my-4">
        <h2 class="text-center mb-4">Our Collection of Women’s Bags</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card">
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Bag Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                                <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No products found in this category.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>
