<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$title = 'Вход';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    if (loginUser($login, $password)) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Вы успешно вошли в систему!'
        ];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль';
    }
}

ob_start();
include 'templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="text-center mb-4">Вход в систему</h2>
        
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label for="login" class="form-label">Логин или Email</label>
                <input type="text" class="form-control" id="login" name="login" required>
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>
        
        <div class="mt-3 text-center">
            Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a>
        </div>
    </div>
</div>

<?php
include 'templates/footer.php';
?>