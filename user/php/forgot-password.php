<?php
// Include your database config
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate
    if (empty($email) || empty($password)) {
        echo "Email and password are required.";
        exit;
    }

    // Securely hash the new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);

    // Execute and respond
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Password updated successfully.";
        } else {
            echo "No account found with that email.";
        }
    } else {
        echo "Failed to update password. Please try again.";
    }

    // Close
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
