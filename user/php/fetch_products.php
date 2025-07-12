<?php
include 'config.php';

// Set proper headers for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $sql = "SELECT * FROM product WHERE status = 'active' ORDER BY id DESC";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Handle the images field - it might be comma-separated or single image
        $image = $row['images'];
        if (strpos($image, ',') !== false) {
            $images = explode(',', $image);
            $image = trim($images[0]); // Use first image
        }
        
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'category' => $row['category'],
            'image' => $image,
            'stock' => $row['stock'],
            'status' => $row['status']
        ];
    }

    echo json_encode($products);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
