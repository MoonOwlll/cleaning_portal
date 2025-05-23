<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$title = 'Админ-панель';
$orders = getAllOrders();
$users = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
$newOrders = $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'new'")->fetch();

ob_start();
include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Всего пользователей</h5>
                <p class="card-text display-4"><?= $users['count'] ?></p>
                <a href="users.php" class="text-white">Подробнее</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Новые заказы</h5>
                <p class="card-text display-4"><?= $newOrders['count'] ?></p>
                <a href="orders.php" class="text-dark">Подробнее</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Выполнено заказов</h5>
                <p class="card-text display-4"><?= $pdo->query("SELECT COUNT(*) as count FROM orders WHERE status = 'completed'")->fetch()['count'] ?></p>
                <a href="orders.php?status=completed" class="text-white">Подробнее</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Последние заказы</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Клиент</th>
                        <th>Услуга</th>
                        <th>Дата/Время</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['user_name']) ?></td>
                        <td><?= htmlspecialchars($order['service_name']) ?></td>
                        <td>
                            <?= date('d.m.Y', strtotime($order['desired_date'])) ?>
                            <?= date('H:i', strtotime($order['desired_time'])) ?>
                        </td>
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
        <a href="orders.php" class="btn btn-primary mt-3">Все заказы</a>
    </div>
</div>

<?php
include '../templates/footer.php';
?>