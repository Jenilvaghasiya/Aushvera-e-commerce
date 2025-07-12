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

// Handle GET request - fetch addresses
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    
    // Check if specific address is requested
    if (isset($_GET['id'])) {
        $address_id = $_GET['id'];
        $stmt = $conn->prepare('SELECT * FROM user_addresses WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $address_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $address = $result->fetch_assoc();
        
        if ($address) {
            echo json_encode(['success' => true, 'address' => $address]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Address not found']);
        }
        $stmt->close();
    } else {
        // Fetch all addresses for user
        $stmt = $conn->prepare('SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC');
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $addresses = [];
        
        while ($row = $result->fetch_assoc()) {
            $addresses[] = $row;
        }
        
        echo json_encode(['success' => true, 'addresses' => $addresses]);
        $stmt->close();
    }
    $conn->close();
    exit();
}

// Handle POST request - add/edit/delete address
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        
        // Check if it's a JSON request (for delete operations)
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if ($data && isset($data['action']) && $data['action'] === 'delete') {
            // Delete address
            $address_id = $data['address_id'];
            $stmt = $conn->prepare('DELETE FROM user_addresses WHERE id = ? AND user_id = ?');
            $stmt->bind_param('ii', $address_id, $user_id);
            $success = $stmt->execute();
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Address deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete address']);
            }
            $stmt->close();
        } else {
            // Add/Edit address
            $address_id = $_POST['address_id'] ?? null;
            $address_type = $_POST['address_type'] ?? '';
            $full_name = $_POST['full_name'] ?? '';
            $phone_number = $_POST['phone_number'] ?? '';
            $address_line1 = $_POST['address_line1'] ?? '';
            $address_line2 = $_POST['address_line2'] ?? '';
            $city = $_POST['city'] ?? '';
            $state = $_POST['state'] ?? '';
            $pincode = $_POST['pincode'] ?? '';
            $country = $_POST['country'] ?? 'India';
            $is_default = isset($_POST['is_default']) ? 1 : 0;
            
            // If setting as default, unset other defaults first
            if ($is_default) {
                $stmt = $conn->prepare('UPDATE user_addresses SET is_default = 0 WHERE user_id = ?');
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->close();
            }
            
            if ($address_id) {
                // Update existing address
                $stmt = $conn->prepare('UPDATE user_addresses SET address_type=?, full_name=?, phone_number=?, address_line1=?, address_line2=?, city=?, state=?, pincode=?, country=?, is_default=?, updated_at=NOW() WHERE id=? AND user_id=?');
                $stmt->bind_param('sssssssssiii', $address_type, $full_name, $phone_number, $address_line1, $address_line2, $city, $state, $pincode, $country, $is_default, $address_id, $user_id);
                $success = $stmt->execute();
            } else {
                // Insert new address
                $stmt = $conn->prepare('INSERT INTO user_addresses (user_id, address_type, full_name, phone_number, address_line1, address_line2, city, state, pincode, country, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('isssssssssi', $user_id, $address_type, $full_name, $phone_number, $address_line1, $address_line2, $city, $state, $pincode, $country, $is_default);
                $success = $stmt->execute();
            }
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Address saved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save address']);
            }
            $stmt->close();
        }
    } else {
        // Regular form submission - redirect to profile page
        $address_id = $_POST['address_id'] ?? null;
        $address_type = $_POST['address_type'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $phone_number = $_POST['phone_number'] ?? '';
        $address_line1 = $_POST['address_line1'] ?? '';
        $address_line2 = $_POST['address_line2'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        $pincode = $_POST['pincode'] ?? '';
        $country = $_POST['country'] ?? 'India';
        $is_default = isset($_POST['is_default']) ? 1 : 0;
        
        // If setting as default, unset other defaults first
        if ($is_default) {
            $stmt = $conn->prepare('UPDATE user_addresses SET is_default = 0 WHERE user_id = ?');
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();
        }
        
        if ($address_id) {
            // Update existing address
            $stmt = $conn->prepare('UPDATE user_addresses SET address_type=?, full_name=?, phone_number=?, address_line1=?, address_line2=?, city=?, state=?, pincode=?, country=?, is_default=?, updated_at=NOW() WHERE id=? AND user_id=?');
            $stmt->bind_param('sssssssssiii', $address_type, $full_name, $phone_number, $address_line1, $address_line2, $city, $state, $pincode, $country, $is_default, $address_id, $user_id);
            $success = $stmt->execute();
        } else {
            // Insert new address
            $stmt = $conn->prepare('INSERT INTO user_addresses (user_id, address_type, full_name, phone_number, address_line1, address_line2, city, state, pincode, country, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('isssssssssi', $user_id, $address_type, $full_name, $phone_number, $address_line1, $address_line2, $city, $state, $pincode, $country, $is_default);
            $success = $stmt->execute();
        }
        
        if ($success) {
            header('Location: ../profile.html?address_success=1');
        } else {
            header('Location: ../profile.html?address_error=1');
        }
    }
    $conn->close();
    exit();
}
?> 