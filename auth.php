<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $response = [];
    
    try {
        $db = new Database();
        
        switch ($action) {
            case 'login':
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                $user = $db->getUserByEmail($email);
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['name'];
                    
                    $response = [
                        'success' => true,
                        'message' => 'Авторизация успешна',
                        'redirect' => 'dashboard.php'
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Неверный email или пароль'
                    ];
                }
                break;
                
            case 'register':
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if ($password !== $confirmPassword) {
                    $response = [
                        'success' => false,
                        'message' => 'Пароли не совпадают'
                    ];
                } else {
                    if ($db->createUser($name, $email, $password)) {
                        $response = [
                            'success' => true,
                            'message' => 'Регистрация успешна',
                            'redirect' => 'login.php'
                        ];
                    } else {
                        $response = [
                            'success' => false,
                            'message' => 'Ошибка регистрации. Возможно, пользователь с таким email уже существует.'
                        ];
                    }
                }
                break;
                
            default:
                $response = [
                    'success' => false,
                    'message' => 'Неизвестное действие'
                ];
        }
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Ошибка сервера: ' . $e->getMessage()
        ];
    }
    
    echo json_encode($response);
    exit;
}