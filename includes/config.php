<?php
// Настройки приложения
define('APP_NAME', 'Портал клининговых услуг "Мой Не Сам"');
define('APP_ROOT', dirname(dirname(__FILE__)));
define('APP_URL', 'http://cleaning-portal');

// Настройки сессии
session_start();

// Подключение к базе данных
require_once 'db.php';

// Функции аутентификации
require_once 'auth.php';

// Вспомогательные функции
require_once 'functions.php';
?>