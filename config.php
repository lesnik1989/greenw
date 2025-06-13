<?php
// Конфигурация базы данных
define('DB_HOST', 'localhost');
define('DB_NAME', 'cq75327_green');
define('DB_USER', 'cq75327_green');
define('DB_PASS', 'Davinci-2020');

// Настройки приложения
define('SITE_URL', 'https://stroi-domovoi.ru');
define('ADMIN_EMAIL', 'arslan-sk@yandex.ru');

// Настройки премиум доступа
$premiumPlans = [
    'basic' => [
        'name' => 'Базовый',
        'price' => 1490,
        'duration' => 3, // месяца
        'calculations' => 50 // лимит расчетов
    ],
    'pro' => [
        'name' => 'Профессиональный',
        'price' => 2990,
        'duration' => 6,
        'calculations' => 200
    ],
    'vip' => [
        'name' => 'Лидерский',
        'price' => 4990,
        'duration' => 12,
        'calculations' => 1000
    ]
];

// Настройки оплаты
$paymentDetails = [
    'bank_name' => 'Сбербанк',
    'account_number' => '2202 2036 5758 0470',
    'recipient' => 'Яппаров Фидан Фанилович',
    'recipient_inn' => '024202124104'
];

// Секретный ключ для генерации токенов
define('SECRET_KEY', '20021989');

// Включить вывод ошибок (отключить в продакшене)
ini_set('display_errors', 1);
error_reporting(E_ALL);


?>