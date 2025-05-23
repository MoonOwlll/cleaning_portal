<?php
// Получение списка услуг
function getServices() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM services");
    return $stmt->fetchAll();
}

// Создание нового заказа
function createOrder($data) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, service_id, address, contact_phone, desired_date, desired_time, payment_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $_SESSION['user_id'],
        $data['service_id'],
        $data['address'],
        $data['contact_phone'],
        $data['desired_date'],
        $data['desired_time'],
        $data['payment_type']
    ]);
}

// Получение заказов пользователя
function getUserOrders($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT o.*, s.name as service_name, s.price 
        FROM orders o 
        JOIN services s ON o.service_id = s.id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Получение всех заказов (для администратора)
function getAllOrders() {
    global $pdo;
    
    $stmt = $pdo->query("
        SELECT o.*, u.full_name as user_name, s.name as service_name, s.price 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        JOIN services s ON o.service_id = s.id 
        ORDER BY o.created_at DESC
    ");
    return $stmt->fetchAll();
}

// Обновление статуса заказа
function updateOrderStatus($orderId, $status, $reason = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE orders SET status = ?, cancellation_reason = ?, updated_at = NOW() WHERE id = ?");
    return $stmt->execute([$status, $reason, $orderId]);
}

// Получение информации о пользователе
function getUserById($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

// Получение текста статуса
function getStatusText($status) {
    switch ($status) {
        case 'new':
            return 'Новый';
        case 'confirmed':
            return 'Подтвержден';
        case 'completed':
            return 'Выполнен';
        case 'cancelled':
            return 'Отменен';
        default:
            return $status;
    }
}

// Проверка доступа к заказу
function canViewOrder($orderId, $userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, $userId]);
    return $stmt->fetchColumn() > 0;
}
?>