<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$title = 'Профиль';
$user = getUserById($_SESSION['user_id']);

ob_start();
include 'templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Профиль пользователя</h2>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Личные данные</h5>
                        <p><strong>ФИО:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                        <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Логин:</strong> <?= htmlspecialchars($user['login']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="card-title">Статистика</h5>
                        <?php
                        $orders = getUserOrders($_SESSION['user_id']);
                        $totalOrders = count($orders);
                        $completedOrders = 0;
                        
                        foreach ($orders as $order) {
                            if ($order['status'] === 'completed') {
                                $completedOrders++;
                            }
                        }
                        ?>
                        <p><strong>Всего заказов:</strong> <?= $totalOrders ?></p>
                        <p><strong>Выполнено заказов:</strong> <?= $completedOrders ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'templates/footer.php';
?>