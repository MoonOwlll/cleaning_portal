<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

$title = 'Управление заказами';
$status = $_GET['status'] ?? 'all';

$query = "
    SELECT o.*, u.full_name as user_name, s.name as service_name, s.price 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    JOIN services s ON o.service_id = s.id
";

if (in_array($status, ['new', 'confirmed', 'completed', 'cancelled'])) {
    $query .= " WHERE o.status = ?";
    $orders = $pdo->prepare($query);
    $orders->execute([$status]);
} else {
    $orders = $pdo->query($query);
}

$orders = $orders->fetchAll();

ob_start();
include '../templates/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Управление заказами</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="btn-group" role="group">
                    <a href="?status=all" class="btn btn-outline-secondary <?= $status === 'all' ? 'active' : '' ?>">Все</a>
                    <a href="?status=new" class="btn btn-outline-primary <?= $status === 'new' ? 'active' : '' ?>">Новые</a>
                    <a href="?status=confirmed" class="btn btn-outline-warning <?= $status === 'confirmed' ? 'active' : '' ?>">Подтвержденные</a>
                    <a href="?status=completed" class="btn btn-outline-success <?= $status === 'completed' ? 'active' : '' ?>">Выполненные</a>
                    <a href="?status=cancelled" class="btn btn-outline-danger <?= $status === 'cancelled' ? 'active' : '' ?>">Отмененные</a>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Клиент</th>
                                <th>Услуга</th>
                                <th>Дата/Время</th>
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
                                <td><?= htmlspecialchars($order['user_name']) ?></td>
                                <td><?= htmlspecialchars($order['service_name']) ?></td>
                                <td>
                                    <?= date('d.m.Y', strtotime($order['desired_date'])) ?>
                                    <?= date('H:i', strtotime($order['desired_time'])) ?>
                                </td>
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
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-outline-primary">Просмотр</a>
                                        <div class="dropdown">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Изменить
                                            </button>
                                            <ul class="dropdown-menu">
                                                <?php if ($order['status'] !== 'confirmed'): ?>
                                                <li><a class="dropdown-item" href="update_order.php?id=<?= $order['id'] ?>&status=confirmed">Подтвердить</a></li>
                                                <?php endif; ?>
                                                <?php if ($order['status'] !== 'completed'): ?>
                                                <li><a class="dropdown-item" href="update_order.php?id=<?= $order['id'] ?>&status=completed">Завершить</a></li>
                                                <?php endif; ?>
                                                <?php if ($order['status'] !== 'cancelled'): ?>
                                                <li><a class="dropdown-item" href="cancel_order.php?id=<?= $order['id'] ?>">Отменить</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../templates/footer.php';
?>