<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header('Location: orders.php');
    exit;
}

$orderId = (int)$_GET['id'];
$status = $_GET['status'];

if (!in_array($status, ['confirmed', 'completed'])) {
    header('Location: orders.php');
    exit;
}

updateOrderStatus($orderId, $status);

$_SESSION['alert'] = [
    'type' => 'success',
    'message' => 'Статус заказа успешно обновлен.'
];

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'orders.php'));
exit;
?>