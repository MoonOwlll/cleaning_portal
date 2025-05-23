<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit;
}

$orderId = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT o.*, u.full_name as user_name, u.phone as user_phone, u.email as user_email, 
           s.name as service_name, s.price, s.description as service_description
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    JOIN services s ON o.service_id = s.id
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Заказ не найден.'
    ];
    header('Location: orders.php');
    exit;
}

$title = 'Заказ #' . $order['id'];

ob_start();
include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <h2 class="mb-4">Заказ #<?= $order['id'] ?></h2>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Информация о клиенте</h5>
            </div>
            <div class="card-body">
                <p><strong>ФИО:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($order['user_phone']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['user_email']) ?></p>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Информация о заказе</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Услуга:</strong> <?= htmlspecialchars($order['service_name']) ?></p>
                        <p><strong>Описание:</strong> <?= htmlspecialchars($order['service_description']) ?></p>
                        <p><strong>Стоимость:</strong> <?= $order['price'] ?> ₽</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Дата:</strong> <?= date('d.m.Y', strtotime($order['desired_date'])) ?></p>
                        <p><strong>Время:</strong> <?= date('H:i', strtotime($order['desired_time'])) ?></p>
                        <p><strong>Адрес:</strong> <?= htmlspecialchars($order['address']) ?></p>
                        <p><strong>Контактный телефон:</strong> <?= htmlspecialchars($order['contact_phone']) ?></p>
                        <p><strong>Способ оплаты:</strong> <?= $order['payment_type'] === 'cash' ? 'Наличными' : 'Банковской картой' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Управление заказом</h5>
            </div>
            <div class="card-body">
                <?php 
                $statusClass = '';
                switch ($order['status']) {
                    case 'new':
                        $statusClass = 'bg-primary';
                        break;
                    case 'confirmed':
                        $statusClass = 'bg-warning text-dark';
                        break;
                    case 'completed':
                        $statusClass = 'bg-success';
                        break;
                    case 'cancelled':
                        $statusClass = 'bg-danger';
                        break;
                }
                ?>
                <p>
                    <strong>Статус:</strong> 
                    <span class="badge <?= $statusClass ?>"><?= getStatusText($order['status']) ?></span>
                </p>
                
                <?php if ($order['status'] === 'cancelled' && !empty($order['cancellation_reason'])): ?>
                <p><strong>Причина отмены:</strong> <?= htmlspecialchars($order['cancellation_reason']) ?></p>
                <?php endif; ?>
                
                <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                <?php if ($order['created_at'] != $order['updated_at']): ?>
                <p><strong>Последнее обновление:</strong> <?= date('d.m.Y H:i', strtotime($order['updated_at'])) ?></p>
                <?php endif; ?>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <?php if ($order['status'] !== 'confirmed'): ?>
                    <a href="update_order.php?id=<?= $order['id'] ?>&status=confirmed" class="btn btn-warning">Подтвердить</a>
                    <?php endif; ?>
                    
                    <?php if ($order['status'] !== 'completed'): ?>
                    <a href="update_order.php?id=<?= $order['id'] ?>&status=completed" class="btn btn-success">Завершить</a>
                    <?php endif; ?>
                    
                    <?php if ($order['status'] !== 'cancelled'): ?>
                    <a href="cancel_order.php?id=<?= $order['id'] ?>" class="btn btn-danger">Отменить</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="orders.php" class="btn btn-outline-secondary w-100">Вернуться к списку</a>
        </div>
    </div>
</div>

<?php
include '../templates/footer.php';
?>