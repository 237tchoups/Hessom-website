<?php
header('Content-Type: application/json');
require_once 'functions.php';

$result = markNotificationsAsRead();

echo json_encode([
    'success' => $result
]);
?>