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

if (!isset($data['orderId']) || !isset($data['paymentMethod']) || !isset($data['amount'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$orderId = $data['orderId'];
$paymentMethod = $data['paymentMethod'];
$amount = $data['amount'];
$paymentDetails = isset($data['paymentDetails']) ? $data['paymentDetails'] : '';

// Generate transaction ID
$transactionId = 'TXN-' . mt_rand(100000, 999999);

// Record payment
$result = recordPayment($orderId, $transactionId, $paymentMethod, $paymentDetails, $amount);

if (!$result) {
    echo json_encode(['error' => 'Failed to process payment']);
    exit;
}

// Get order details
$order = getOrderById($orderId);

// Add notification
addNotification(
    'Payment Received',
    "Payment of {$amount} FCFA received for order #{$order['order_number']}"
);

echo json_encode([
    'success' => true,
    'transactionId' => $transactionId,
    'order' => $order
]);
?>