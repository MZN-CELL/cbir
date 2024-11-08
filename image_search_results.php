<?php
include 'includes/db_connect.php';

// Initialize variables
$image_path = '';
$matched_products = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image_search'])) {
    $upload_dir = 'uploads/'; // Ensure this directory exists and is writable
    $image_path = $upload_dir . basename($_FILES['image_search']['name']);

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($_FILES['image_search']['tmp_name'], $image_path)) {
        // Extract HOG features using the Python script
        $hog_features = shell_exec("python extract_hog.py $image_path 2>&1"); // Capture output and errors
        $hog_features = json_decode($hog_features); // Convert JSON output to array

        // Check if features were extracted successfully
        if (!is_array($hog_features)) {
            echo "<div class='container mt-4'><h4>Failed to extract HOG features from the uploaded image.</h4></div>";
            exit();
        }
    } else {
        echo "<div class='container mt-4'><h4>Error uploading image.</h4></div>";
        exit();
    }

    // Fetch all products to compare
    $sql = "SELECT * FROM bags"; // Adjust this query if needed
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
        if (isset($row['hog_features'])) {
            $db_hog_features = json_decode($row['hog_features']); // Assuming hog_features is stored as JSON
            
            // Ensure both features are arrays
            if (is_array($db_hog_features) && is_array($hog_features)) {
                // Calculate similarity using Euclidean distance
                $distance = calculate_distance($db_hog_features, $hog_features);
                
                // Define a threshold for similarity
                if ($distance < 0.1) { // Adjust this threshold as needed
                    $matched_products[] = $row; // Store matched product
                }
            }
        }
    }
}

// Function to calculate Euclidean distance
function calculate_distance($features1, $features2) {
    return sqrt(array_sum(array_map(function($a, $b) {
        return pow($a - $b, 2);
    }, $features1, $features2)));
}

// Display matched products
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Search Results</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h4>Search Results for Uploaded Image:</h4>
        <div class="row">
            <?php if (empty($matched_products)): ?>
                <p>No similar products found.</p>
            <?php else: ?>
                <?php foreach ($matched_products as $product): ?>
                    <div class="col-md-3">
                        <div class="card">
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="Bag Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
