<?php
require_once 'config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

// Check if email exists
$sql = "SELECT id FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: ../signup.html?error=1");
    exit();
}

// For demo: store plaintext password. For production, use password_hash!
$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $password);

if ($stmt->execute()) {
    header("Location: ../index.html?success=1");
    exit();
} else {
    header("Location: ../signup.html?error=1");
    exit();
}
?>