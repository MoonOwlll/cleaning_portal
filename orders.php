<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$title = 'Мои заказы';
$orders = getUserOrders($_SESSION['user_id']);

ob_start();
include 'templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Мои заказы</h2>
        
        <?php if (empty($orders)): ?>
        <div class="alert alert-info">
            У вас пока нет заказов. <a href="create_order.php" class="alert-link">Создайте первый заказ</a>.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Услуга</th>
                        <th>Дата</th>
                        <th>Время</th>
                        <th>Адрес</th>
                        <th>Стоимость</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['service_name']) ?></td>
                        <td><?= date('d.m.Y', strtotime($order['desired_date'])) ?></td>
                        <td><?= date('H:i', strtotime($order['desired_time'])) ?></td>
                        <td><?= htmlspecialchars($order['address']) ?></td>
                        <td><?= $order['price'] ?> ₽</td>
                        <td>
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
                            <span class="badge <?= $statusClass ?>"><?= getStatusText($order['status']) ?></span>
                        </td>
                        <td>
                            <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Просмотр</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'templates/footer.php';
?>