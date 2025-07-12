<?php
include 'config.php'; // Make sure this file defines $conn

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password']; // Plain text password from form

    // Update password directly (no hashing)
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $password, $email);

    if ($stmt->execute()) {
        echo "Password updated successfully.";
    } else {
        echo "Error updating password. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
