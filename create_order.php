<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$title = 'Создание заказа';
$services = getServices();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'service_id' => (int)$_POST['service_id'],
        'address' => trim($_POST['address']),
        'contact_phone' => trim($_POST['contact_phone']),
        'desired_date' => $_POST['desired_date'],
        'desired_time' => $_POST['desired_time'],
        'payment_type' => $_POST['payment_type']
    ];
    
    // Валидация данных
    $errors = [];
    
    if (empty($data['service_id'])) {
        $errors[] = 'Выберите услугу';
    }
    
    if (empty($data['address'])) {
        $errors[] = 'Укажите адрес';
    }
    
    if (empty($data['contact_phone'])) {
        $errors[] = 'Укажите контактный телефон';
    }
    
    if (empty($data['desired_date']) || strtotime($data['desired_date']) < strtotime('today')) {
        $errors[] = 'Укажите корректную дату';
    }
    
    if (empty($data['desired_time'])) {
        $errors[] = 'Укажите время';
    }
    
    if (empty($errors)) {
        if (createOrder($data)) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Заказ успешно создан!'
            ];
            header('Location: orders.php');
            exit;
        } else {
            $errors[] = 'Ошибка при создании заказа. Попробуйте позже.';
        }
    }
}

ob_start();
include 'templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Создание нового заказа</h2>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label for="service_id" class="form-label">Услуга</label>
                <select class="form-select" id="service_id" name="service_id" required>
                    <option value="">Выберите услугу</option>
                    <?php foreach ($services as $service): ?>
                    <option value="<?= $service['id'] ?>" <?= isset($data['service_id']) && $data['service_id'] == $service['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($service['name']) ?> (<?= $service['price'] ?> ₽)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Адрес уборки</label>
                <textarea class="form-control" id="address" name="address" rows="2" required><?= $data['address'] ?? '' ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="contact_phone" class="form-label">Контактный телефон</label>
                <input type="tel" class="form-control" id="contact_phone" name="contact_phone" value="<?= $data['contact_phone'] ?? '' ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="desired_date" class="form-label">Желаемая дата</label>
                    <input type="date" class="form-control" id="desired_date" name="desired_date" min="<?= date('Y-m-d') ?>" value="<?= $data['desired_date'] ?? '' ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="desired_time" class="form-label">Желаемое время</label>
                    <input type="time" class="form-control" id="desired_time" name="desired_time" value="<?= $data['desired_time'] ?? '10:00' ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Способ оплаты</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_type" id="payment_cash" value="cash" <?= (!isset($data['payment_type']) || $data['payment_type'] === 'cash') ? 'checked' : '' ?>>
                    <label class="form-check-label" for="payment_cash">Наличными</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_type" id="payment_card" value="card" <?= isset($data['payment_type']) && $data['payment_type'] === 'card' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="payment_card">Банковской картой</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Создать заказ</button>
        </form>
    </div>
</div>

<?php
include 'templates/footer.php';
?>