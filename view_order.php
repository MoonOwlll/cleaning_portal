<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: orders.php');
    exit;
}

$orderId = (int)$_GET['id'];
$orders = getUserOrders($_SESSION['user_id']);
$order = null;

foreach ($orders as $o) {
    if ($o['id'] == $orderId) {
        $order = $o;
        break;
    }
}

if (!$order) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Заказ не найден или у вас нет к нему доступа.'
    ];
    header('Location: orders.php');
    exit;
}

$title = 'Заказ #' . $order['id'];

ob_start();
include 'templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Заказ #<?= $order['id'] ?></h2>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Информация о заказе</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Услуга:</strong> <?= htmlspecialchars($order['service_name']) ?></p>
                        <p><strong>Дата:</strong> <?= date('d.m.Y', strtotime($order['desired_date'])) ?></p>
                        <p><strong>Время:</strong> <?= date('H:i', strtotime($order['desired_time'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Адрес:</strong> <?= htmlspecialchars($order['address']) ?></p>
                        <p><strong>Телефон:</strong> <?= htmlspecialchars($order['contact_phone']) ?></p>
                        <p><strong>Способ оплаты:</strong> <?= $order['payment_type'] === 'cash' ? 'Наличными' : 'Банковской картой' ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Статус заказа</h5>
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
                    <span class="badge <?= $statusClass ?>"><?= getStatusText($order['status']) ?></span>
                    <?php if ($order['status'] === 'cancelled' && !empty($order['cancellation_reason'])): ?>
                    <br><strong>Причина отмены:</strong> <?= htmlspecialchars($order['cancellation_reason']) ?>
                    <?php endif; ?>
                </p>
                
                <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></p>
                <?php if ($order['created_at'] != $order['updated_at']): ?>
                <p><strong>Последнее обновление:</strong> <?= date('d.m.Y H:i', strtotime($order['updated_at'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="orders.php" class="btn btn-outline-secondary">Вернуться к списку заказов</a>
        </div>
    </div>
</div>

<?php
include 'templates/footer.php';
?>