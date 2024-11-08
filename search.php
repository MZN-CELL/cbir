<?php
include 'includes/db_connect.php';
include 'includes/header.php';

$query = $_GET['query'] ?? '';
$image = $_FILES['image_search']['tmp_name'] ?? null;

if ($image) {
    // Handle image upload for search
    $upload_dir = 'uploads/';
    $image_path = $upload_dir . basename($_FILES['image_search']['name']);

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($_FILES['image_search']['tmp_name'], $image_path)) {
        // Extract HOG features using the Python script
        $hog_features = shell_exec("python extract_hog.py $image_path");
        $hog_features = json_decode($hog_features); // Convert JSON output to array

        // Check if features were extracted successfully
        if ($hog_features) {
            // Fetch all products to compare
            $sql = "SELECT * FROM bags"; // Adjust this query if needed
            $result = $conn->query($sql);

            $matched_products = []; // Array to store matched products

            while ($row = $result->fetch_assoc()) {
                $db_hog_features = json_decode($row['hog_features']); // Assuming hog_features is stored as JSON in the database
                
                // Calculate similarity using Euclidean distance
                $distance = calculate_distance($db_hog_features, $hog_features);
                
                // Define a threshold for similarity
                if ($distance < 0.1) { // Adjust threshold based on your needs
                    $matched_products[] = $row; // Store matched product
                }
            }

            // Display matched products
            echo "<div class='container mt-4'>";
            echo "<h4>Search Results for Uploaded Image:</h4>";
            echo "<div class='row'>";
            foreach ($matched_products as $product) {
                echo "<div class='col-md-3'>";
                echo "<div class='card'>";
                echo "<img src='images/{$product['image']}' class='card-img-top' alt='Bag Image'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>{$product['name']}</h5>";
                echo "<p class='card-text'>Rp " . number_format($product['price'], 0, ',', '.') . "</p>";
                echo "<a href='product.php?id={$product['id']}' class='btn btn-primary'>View Details</a>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div></div>";
        } else {
            echo "<div class='container mt-4'><h4>Failed to extract HOG features from the uploaded image.</h4></div>";
        }
    } else {
        echo "<div class='container mt-4'><h4>Error uploading image.</h4></div>";
    }
} elseif ($query) {
    $sql = "SELECT * FROM bags WHERE name LIKE '%$query%' OR description LIKE '%$query%'";
    $result = $conn->query($sql);

    echo "<div class='container mt-4'>";
    echo "<h4>Search Results for \"$query\":</h4>";
    echo "<div class='row'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='col-md-3'>";
        echo "<div class='card'>";
        echo "<img src='images/{$row['image']}' class='card-img-top' alt='Bag Image'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>{$row['name']}</h5>";
        echo "<p class='card-text'>Rp " . number_format($row['price'], 0, ',', '.') . "</p>";
        echo "<a href='product.php?id={$row['id']}' class='btn btn-primary'>View Details</a>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div></div>";
} else {
    echo "<div class='container mt-4'><h4>Please enter a search term or upload an image.</h4></div>";
}

include 'includes/footer.php';

// Function to calculate Euclidean distance
function calculate_distance($features1, $features2) {
    return sqrt(array_sum(array_map(function($a, $b) {
        return pow($a - $b, 2);
    }, $features1, $features2)));
}
?>
