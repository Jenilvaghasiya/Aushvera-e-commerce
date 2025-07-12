<?php
include 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Get cart items (this would typically be stored in session or database)
        echo json_encode(['message' => 'Cart operations endpoint']);
        break;
        
    case 'POST':
        // Add item to cart
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action']) && $data['action'] === 'add') {
            $productId = $data['product_id'];
            $quantity = $data['quantity'] ?? 1;
            
            // Here you would typically add to database or session
            // For now, we'll just return success
            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart',
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
        break;
        
    case 'PUT':
        // Update cart item
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action']) && $data['action'] === 'update') {
            $productId = $data['product_id'];
            $quantity = $data['quantity'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated',
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
        break;
        
    case 'DELETE':
        // Remove item from cart
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($data['action']) && $data['action'] === 'remove') {
            $productId = $data['product_id'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Product removed from cart',
                'product_id' => $productId
            ]);
        } else {
            echo json_encode(['error' => 'Invalid action']);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?> 