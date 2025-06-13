<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/db.php';

session_start();

// Только администратор может активировать ключи
// Здесь должна быть проверка на администратора

$db = new Database();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'] ?? '';
    $plan = $_POST['plan'] ?? '';
    $key = $_POST['key'] ?? '';
    
    // В реальной системе ключ должен проверяться по базе оплат
    if ($key === 'ВАШ_СЕКРЕТНЫЙ_КЛЮЧ') {
        if (array_key_exists($plan, $premiumPlans)) {
            $planData = $premiumPlans[$plan];
            if ($db->createPremiumSubscription($userId, $plan, $planData['duration'], $planData['calculations'])) {
                $message = 'Подписка успешно активирована!';
            } else {
                $message = 'Ошибка активации подписки.';
            }
        } else {
            $message = 'Неверный тарифный план.';
        }
    } else {
        $message = 'Неверный ключ активации.';
    }
}

include __DIR__ . '/templates/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3>Активация премиум доступа</h3>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-info"><?= $message ?></div>
                    <?php endif; ?>
                    <form method="POST" action="activate.php">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">ID пользователя</label>
                            <input type="text" class="form-control" id="user_id" name="user_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="plan" class="form-label">Тарифный план</label>
                            <select class="form-select" id="plan" name="plan" required>
                                <option value="basic">Базовый</option>
                                <option value="pro">Профессиональный</option>
                                <option value="vip">Лидерский</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="key" class="form-label">Ключ активации</label>
                            <input type="text" class="form-control" id="key" name="key" required>
                        </div>
                        <button type="submit" class="btn btn-success">Активировать</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>