<?php
require_once 'includes/config.php';

$title = 'Главная';
$services = getServices();

ob_start();
include 'templates/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <h1>Добро пожаловать в "Мой Не Сам"</h1>
        <p class="lead">Профессиональные клининговые услуги для вашего дома и офиса</p>
        
        <?php if (!isLoggedIn()): ?>
        <div class="alert alert-info">
            <p>Для оформления заказа необходимо <a href="register.php" class="alert-link">зарегистрироваться</a> или <a href="login.php" class="alert-link">войти</a> в систему.</p>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Наши услуги</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($services as $service): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($service['name']) ?>
                        <span class="badge bg-primary rounded-pill"><?= $service['price'] ?> ₽</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
include 'templates/footer.php';
?>