<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit;
}

$userId = (int)$_GET['id'];

$stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
$stmt->execute([$userId]);

$_SESSION['alert'] = [
    'type' => 'success',
    'message' => 'Пользователь успешно назначен администратором.'
];

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'users.php'));
exit;
?>