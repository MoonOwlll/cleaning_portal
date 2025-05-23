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
$user = getUserById($userId);

if (!$user) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Пользователь не найден.'
    ];
    header('Location: users.php');
    exit;
}

$ordersCount = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
$ordersCount->execute([$userId]);
$ordersCount = $ordersCount->fetch()['count'];

$title = 'Пользователь: ' . htmlspecialchars($user['full_name']);

ob_start();
include '../templates/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <h2 class="mb-4">Пользователь: <?= htmlspecialchars($user['full_name']) ?></h2>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Основная информация</h5>
            </div>
            <div class="card-body">
                <p><strong>Логин:</strong> <?= htmlspecialchars($user['login']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                <p>
                    <strong>Роль:</strong> 
                    <span class="badge <?= $user['role'] === 'admin' ? 'bg-success' : 'bg-primary' ?>">
                        <?= $user['role'] === 'admin' ? 'Админ' : 'Пользователь' ?>
                    </span>
                </p>
                <p><strong>Дата регистрации:</strong> <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Статистика</h5>
            </div>
            <div class="card-body">
                <p><strong>Всего заказов:</strong> <?= $ordersCount ?></p>
                
                <?php if ($ordersCount > 0): ?>
                <?php
                $statuses = $pdo->prepare("
                    SELECT status, COUNT(*) as count 
                    FROM orders 
                    WHERE user_id = ? 
                    GROUP BY status
                ");
                $statuses->execute([$userId]);
                $statuses = $statuses->fetchAll();
                ?>
                
                <p><strong>Статусы заказов:</strong></p>
                <ul>
                    <?php foreach ($statuses as $status): ?>
                    <li>
                        <?= getStatusText($status['status']) ?>: <?= $status['count'] ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <?php if ($user['role'] !== 'admin'): ?>
                    <a href="make_admin.php?id=<?= $user['id'] ?>" class="btn btn-success">Назначить администратором</a>
                    <?php endif; ?>
                    <a href="users.php" class="btn btn-outline-secondary">Вернуться к списку</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include '../templates/footer.php';
?>