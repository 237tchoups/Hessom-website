<?php
header('Content-Type: application/json');
require_once 'functions.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['hospitalName']) || !isset($data['hospitalLocation']) || !isset($data['items']) || empty($data['items'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$hospitalName = $data['hospitalName'];
$hospitalLocation = $data['hospitalLocation'];
$items = $data['items'];

// Calculate total amount
$totalAmount = 0;
foreach ($items as $item) {
    $totalAmount += $item['totalPrice'];
}

// Create order
$orderResult = createOrder($hospitalName, $hospitalLocation, $totalAmount);

if (!$orderResult) {
    echo json_encode(['error' => 'Failed to create order']);
    exit;
}

$orderId = $orderResult['order_id'];

// Add order items
foreach ($items as $item) {
    addOrderItem(
        $orderId,
        $item['productId'],
        $item['name'],
        $item['designation'],
        $item['quantity'],
        $item['unitPrice'],
        $item['totalPrice']
    );
}

// Add notification
addNotification(
    'New Order',
    "New order #{$orderResult['order_number']} from {$hospitalName} for {$totalAmount} FCFA"
);

echo json_encode([
    'success' => true,
    'order' => $orderResult
]);
?>