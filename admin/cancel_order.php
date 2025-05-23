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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason']);
    
    if (empty($reason)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Укажите причину отмены.'
        ];
        header('Location: cancel_order.php?id=' . $orderId);
        exit;
    }
    
    updateOrderStatus($orderId, 'cancelled', $reason);
    
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Заказ успешно отменен.'
    ];
    
    header('Location: orders.php');
    exit;
}

$title = 'Отмена заказа';

ob_start();
include '../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4">Отмена заказа #<?= $orderId ?></h2>
        
        <form method="post">
            <div class="mb-3">
                <label for="reason" class="form-label">Причина отмены</label>
                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-danger">Подтвердить отмену</button>
                <a href="orders.php" class="btn btn-outline-secondary">Отмена</a>
            </div>
        </form>
    </div>
</div>

<?php
include '../templates/footer.php';
?>