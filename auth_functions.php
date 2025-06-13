<?php
require_once 'db.php';
require_once 'helpers.php';

function loginUser($email, $password) {
    $db = new Database();
    $user = $db->getUserByEmail($email);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        return true;
    }
    
    return false;
}

function registerUser($name, $email, $password) {
    $db = new Database();
    $user = $db->getUserByEmail($email);
    
    if ($user) {
        return false; // Пользователь уже существует
    }
    
    return $db->createUser($name, $email, $password);
}
?>