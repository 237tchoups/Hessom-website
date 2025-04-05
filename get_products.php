<?php
header('Content-Type: application/json');
require_once 'functions.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';

if (empty($category)) {
    echo json_encode(['error' => 'Category is required']);
    exit;
}

$products = getProductsByCategory($category);
echo json_encode($products);
?>