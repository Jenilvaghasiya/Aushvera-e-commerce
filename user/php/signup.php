<?php
require_once 'config.php';

$name = $_POST['name'];
$username = $_POST['username'];
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

// Use password_hash for secure password storage
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $username, $email, $hashed_password);

if ($stmt->execute()) {
    header("Location: ../login.html?success=1");
    exit();
} else {
    header("Location: ../signup.html?error=1");
    exit();
}
?>