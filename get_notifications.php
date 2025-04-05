<?php
header('Content-Type: application/json');
require_once 'functions.php';

$notifications = getNotifications();
$unreadCount = 0;

foreach ($notifications as $notification) {
    if ($notification['is_read'] == 0) {
        $unreadCount++;
    }
}

echo json_encode([
    'notifications' => $notifications,
    'unreadCount' => $unreadCount
]);
?>