<?php
require_once 'includes/config.php';

logoutUser();

$_SESSION['alert'] = [
    'type' => 'success',
    'message' => 'Вы успешно вышли из системы.'
];

header('Location: login.php');
exit;
?>