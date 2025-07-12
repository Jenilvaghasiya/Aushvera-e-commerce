<?php
include 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No product ID provided']);
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM product WHERE id = $id LIMIT 1";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    // Handle images (comma separated)
    $images = [];
    if (!empty($row['images'])) {
        $images = explode(',', $row['images']);
        $images = array_map('trim', $images);
    }
    $row['images'] = $images;
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Product not found']);
}
$conn->close();
?>