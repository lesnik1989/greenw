<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

session_start();

// Проверка аутентификации
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(['error' => 'Требуется авторизация'], 401);
    exit;
}

$db = new Database();

// Проверка премиум доступа
$subscription = $db->getUserSubscription($_SESSION['user_id']);
$isPremium = ($subscription !== false);

// Для бесплатных пользователей - ограничение функционала
if (!$isPremium) {
    // Ограничиваем количество полей для бесплатных пользователей
    $allowedFields = ['personalPV', 'groupPV', 'firstLine'];
} else {
    // Проверяем лимит расчетов
    if ($subscription['calculations_used'] >= $subscription['calculation_limit']) {
        sendJsonResponse(['error' => 'Лимит расчетов исчерпан'], 403);
        exit;
    }
    $db->incrementCalculationCount($_SESSION['user_id']);
}

// Получение данных от пользователя
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    sendJsonResponse(['error' => 'Неверные входные данные'], 400);
    exit;
}

// Фильтрация данных для бесплатных пользователей
if (!$isPremium) {
    $filteredData = [];
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $filteredData[$field] = $data[$field];
        }
    }
    $data = $filteredData;
}

// Защищенная функция расчета
function calculateBusinessPlan($data, $isPremium) {
    // Реальная бизнес-логика, недоступная клиенту
    $personalPV = $data['personalPV'] ?? 0;
    $groupPV = $data['groupPV'] ?? 0;
    $firstLine = $data['firstLine'] ?? 0;
    
    // Базовый расчет (доступен всем)
    $personalBonus = max(0, $personalPV - 50) * 0.20 * 90;
    $giftBonus = ($personalPV >= 50) ? 5 * 90 : 0;
    
    if ($personalPV >= 200) {
        $giftBonus += $personalPV * 0.10 * 90;
    } elseif ($personalPV >= 500) {
        $giftBonus += $personalPV * 0.20 * 90;
    }
    
    $groupBonus = $groupPV * 0.12 * 90;
    $mentorBonus = min(2, $firstLine) * 10 * 90 + max(0, $firstLine - 2) * 10 * 90;
    
    $currentIncome = $personalBonus + $giftBonus + $groupBonus + $mentorBonus;
    
    $result = [
        'currentIncome' => $currentIncome,
        'groupIncome' => $groupBonus,
        'mentorIncome' => $mentorBonus,
        'personalBonus' => $personalBonus,
        'giftBonus' => $giftBonus,
        'isPremium' => $isPremium
    ];
    
    // Премиум функции (только для подписчиков)
    if ($isPremium) {
        $structurePV = $data['structurePV'] ?? 0;
        $secondLine = $data['secondLine'] ?? 0;
        
        $leaderBonus = $structurePV * 0.04 * 90;
        $proBonus = ($firstLine >= 5) ? $groupPV * 0.03 * 90 : 0;
        
        $result['leaderIncome'] = $leaderBonus;
        $result['proBonus'] = $proBonus;
        $result['totalIncome'] = $currentIncome + $leaderBonus + $proBonus;
        
        // Прогноз на 6 месяцев
        $growthRate = 1.15; // 15% в месяц
        $forecast = [];
        for ($i = 1; $i <= 6; $i++) {
            $forecast[$i] = $result['totalIncome'] * pow($growthRate, $i);
        }
        $result['forecast'] = $forecast;
    }
    
    return $result;
}

// Выполнение расчета
try {
    $result = calculateBusinessPlan($data, $isPremium);
    $result['isPremium'] = $isPremium;
    sendJsonResponse($result);
} catch (Exception $e) {
    sendJsonResponse(['error' => 'Ошибка расчета: ' . $e->getMessage()], 500);
}
?>