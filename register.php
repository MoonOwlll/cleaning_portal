<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$title = 'Регистрация';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name' => trim($_POST['full_name']),
        'phone' => trim($_POST['phone']),
        'email' => trim($_POST['email']),
        'login' => trim($_POST['login']),
        'password' => $_POST['password'],
        'password_confirm' => $_POST['password_confirm']
    ];
    
    // Валидация данных
    $errors = [];
    
    if (empty($data['full_name'])) {
        $errors[] = 'Укажите ФИО';
    }
    
    if (empty($data['phone'])) {
        $errors[] = 'Укажите телефон';
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Укажите корректный email';
    }
    
    if (empty($data['login'])) {
        $errors[] = 'Укажите логин';
    }
    
    if (strlen($data['password']) < 6) {
        $errors[] = 'Пароль должен содержать не менее 6 символов';
    }
    
    if ($data['password'] !== $data['password_confirm']) {
        $errors[] = 'Пароли не совпадают';
    }
    
    if (empty($errors)) {
        if (registerUser($data)) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Регистрация прошла успешно! Теперь вы можете войти в систему.'
            ];
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Ошибка при регистрации. Возможно, такой логин или email уже существует.';
        }
    }
}

ob_start();
include 'templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="text-center mb-4">Регистрация</h2>
        
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
                <label for="full_name" class="form-label">ФИО</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <div class="mb-3">
                <label for="password_confirm" class="form-label">Подтверждение пароля</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
        </form>
        
        <div class="mt-3 text-center">
            Уже есть аккаунт? <a href="login.php">Войдите</a>
        </div>
    </div>
</div>

<?php
include 'templates/footer.php';
?>