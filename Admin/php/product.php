<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect all inputs
    $name = $_POST['productName'];
    $category = $_POST['productCategory'];
    $description = $_POST['productDescription'];
    $price = $_POST['productPrice'];
    $discount = $_POST['productDiscount'];
    $sku = $_POST['productSKU'];
    $stock = $_POST['productStock'];
    $status = $_POST['productStatus'];

    // Handle multiple image uploads
    $uploadDir = 'uploads/';
    $imagePaths = [];

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['productImages']['tmp_name'] as $index => $tmpName) {
        $fileName = time() . '_' . basename($_FILES['productImages']['name'][$index]);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $targetPath)) {
            $imagePaths[] = $targetPath; // Store relative path
        }
    }

    // Store image paths as comma-separated string or JSON
    $imageString = implode(',', $imagePaths); // Or use json_encode($imagePaths)

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO product (name, category, description, price, discount, sku, stock, status, images) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssddsdss", $name, $category, $description, $price, $discount, $sku, $stock, $status, $imageString);

    if ($stmt->execute()) {
        header("Location: ../home.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
