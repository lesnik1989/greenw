<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

session_start();

// Проверка прав администратора
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Доступ запрещен';
    exit;
}

$db = new Database();

// Получение списка пользователей
$users = [];
try {
    $stmt = $db->connection->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Ошибка при получении списка пользователей: " . $e->getMessage();
}

// Получение списка подписок
$subscriptions = [];
try {
    $stmt = $db->connection->query("SELECT s.*, u.email, u.name 
                                   FROM subscriptions s
                                   JOIN users u ON s.user_id = u.id");
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Ошибка при получении списка подписок: " . $e->getMessage();
}

include __DIR__ . '/templates/header.php';
?>

<div class="container">
    <h1 class="my-4">Административная панель</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h3>Пользователи</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Email</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <a href="activate.php?user_id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">
                                            Активировать премиум
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h3>Активные подписки</h3>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Пользователь</th>
                                <th>Тариф</th>
                                <th>Срок</th>
                                <th>Расчеты</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subscriptions as $sub): ?>
                                <tr>
                                    <td><?= htmlspecialchars($sub['name']) ?></td>
                                    <td><?= $sub['plan'] ?></td>
                                    <td><?= date('d.m.Y', strtotime($sub['end_date'])) ?></td>
                                    <td><?= $sub['calculations_used'] ?> / <?= $sub['calculation_limit'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3>Активация премиум доступа</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="app/premium.php?action=activate">
                <div class="mb-3">
                    <label class="form-label">ID пользователя</label>
                    <input type="text" class="form-control" name="user_id" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Тарифный план</label>
                    <select class="form-select" name="plan" required>
                        <option value="basic">Базовый</option>
                        <option value="pro">Профессиональный</option>
                        <option value="vip">Лидерский</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Секретный ключ</label>
                    <input type="password" class="form-control" name="key" required>
                </div>
                <button type="submit" class="btn btn-success">Активировать</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>