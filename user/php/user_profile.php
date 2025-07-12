<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    // Check if it's an AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
    } else {
        // Regular form submission - redirect to login
        header('Location: ../login.html');
    }
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch and return the user's profile data
    $stmt = $conn->prepare('SELECT phone, date_of_birth, gender, city FROM user_profile WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'profile' => $profile]);
    $stmt->close();
    $conn->close();
    exit();
}

$phone = $_POST['phone'] ?? '';
$date_of_birth = $_POST['date_of_birth'] ?? null;
$gender = $_POST['gender'] ?? '';
$city = $_POST['city'] ?? '';

// Check if profile already exists
$stmt = $conn->prepare('SELECT id FROM user_profile WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update existing profile
    $stmt->close();
    $stmt = $conn->prepare('UPDATE user_profile SET phone=?, date_of_birth=?, gender=?, city=?, updated_at=NOW() WHERE user_id=?');
    $stmt->bind_param('ssssi', $phone, $date_of_birth, $gender, $city, $user_id);
    $success = $stmt->execute();
} else {
    // Insert new profile
    $stmt->close();
    $stmt = $conn->prepare('INSERT INTO user_profile (user_id, phone, date_of_birth, gender, city) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('issss', $user_id, $phone, $date_of_birth, $gender, $city);
    $success = $stmt->execute();
}

// Check if it's an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // AJAX request - return JSON
    header('Content-Type: application/json');
    
    // Fetch the latest profile data
    $profile = null;
    if ($success) {
        $stmt = $conn->prepare('SELECT phone, date_of_birth, gender, city FROM user_profile WHERE user_id = ?');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $profile = $result->fetch_assoc();
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Profile saved', 'profile' => $profile]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save profile']);
    }
} else {
    // Regular form submission - redirect to profile page
    if ($success) {
        header('Location: ../profile.html?success=1');
    } else {
        header('Location: ../profile.html?error=1');
    }
}

if ($stmt) $stmt->close();
$conn->close(); 