<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

session_start();

if (!isAuthenticated()) {
    sendJsonResponse(['error' => 'Требуется авторизация'], 401);
    exit;
}

$db = new Database();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'check':
        $subscription = $db->getUserSubscription($_SESSION['user_id']);
        sendJsonResponse([
            'isPremium' => ($subscription !== false),
            'calculationsLeft' => $subscription ? ($subscription['calculation_limit'] - $subscription['calculations_used']) : 0
        ]);
        break;
        
    case 'activate':
        if (!isset($_SESSION['admin'])) {
            sendJsonResponse(['error' => 'Доступ запрещен'], 403);
            exit;
        }
        
        $userId = $_POST['user_id'] ?? '';
        $plan = $_POST['plan'] ?? '';
        $key = $_POST['key'] ?? '';
        
        if ($key !== SECRET_KEY) {
            sendJsonResponse(['error' => 'Неверный ключ активации'], 400);
            exit;
        }
        
        if (!array_key_exists($plan, $premiumPlans)) {
            sendJsonResponse(['error' => 'Неверный тарифный план'], 400);
            exit;
        }
        
        $planData = $premiumPlans[$plan];
        if ($db->createPremiumSubscription($userId, $plan, $planData['duration'], $planData['calculations'])) {
            sendJsonResponse(['success' => true]);
        } else {
            sendJsonResponse(['error' => 'Ошибка активации подписки'], 500);
        }
        break;
        
    default:
        sendJsonResponse(['error' => 'Неизвестное действие'], 400);
}