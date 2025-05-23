<?php
// Проверка аутентификации пользователя
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Проверка роли пользователя
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

// Регистрация нового пользователя
function registerUser($data) {
    global $pdo;
    
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, email, login, password) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['full_name'],
        $data['phone'],
        $data['email'],
        $data['login'],
        $hashedPassword
    ]);
}

// Аутентификация пользователя
function loginUser($login, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ? OR email = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_role'] = $user['role'];
        return true;
    }
    
    return false;
}

// Выход пользователя
function logoutUser() {
    session_unset();
    session_destroy();
}
?>