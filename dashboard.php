<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/db.php';

session_start();

if (!isAuthenticated()) {
    redirect(SITE_URL . '/login.php');
}

$db = new Database();
$subscription = $db->getUserSubscription($_SESSION['user_id']);

include __DIR__ . '/templates/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5>Профиль пользователя</h5>
                </div>
                <div class="card-body">
                    <p><strong>Имя:</strong> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
                </div>
            </div>
            
            <?php if ($subscription): ?>
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5>Премиум подписка</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Тариф:</strong> <?= $subscription['plan'] ?></p>
                        <p><strong>Осталось расчетов:</strong> <?= $subscription['calculation_limit'] - $subscription['calculations_used'] ?></p>
                        <p><strong>Действует до:</strong> <?= date('d.m.Y', strtotime($subscription['end_date'])) ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5>Премиум доступ</h5>
                    </div>
                    <div class="card-body">
                        <p>У вас нет активной подписки.</p>
                        <a href="payment.php" class="btn btn-success">Приобрести подписку</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>Калькулятор бизнес-плана</h5>
                </div>
                <div class="card-body">
                    <form id="calculationForm">
                        <div class="mb-3">
                            <label class="form-label">Личный объем (PV)</label>
                            <input type="number" class="form-control" id="personalPV" value="50" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Лично-групповой объем (ЛГО, PV)</label>
                            <input type="number" class="form-control" id="groupPV" value="750" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Партнеры в первой линии</label>
                            <input type="number" class="form-control" id="firstLine" value="3" min="0">
                        </div>
                        <?php if ($subscription): ?>
                            <div class="mb-3">
                                <label class="form-label">Структурно-групповой объем (СГО, PV)</label>
                                <input type="number" class="form-control" id="structurePV" value="1500" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Партнеры во второй линии</label>
                                <input type="number" class="form-control" id="secondLine" value="0" min="0">
                            </div>
                        <?php endif; ?>
                    </form>
                    <button class="btn btn-success" id="calculateBtn">Рассчитать</button>
                    <div id="results" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('calculateBtn').addEventListener('click', async () => {
    const formData = {
        personalPV: document.getElementById('personalPV').value,
        groupPV: document.getElementById('groupPV').value,
        firstLine: document.getElementById('firstLine').value,
        <?php if ($subscription): ?>
            structurePV: document.getElementById('structurePV').value,
            secondLine: document.getElementById('secondLine').value
        <?php endif; ?>
    };
    
    try {
        const response = await fetch('<?= SITE_URL ?>/app/calculate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (result.error) {
            alert(`Ошибка: ${result.error}`);
            return;
        }
        
        // Отображаем результаты
        let html = `
            <div class="alert alert-success">
                <h4>Результаты расчета</h4>
                <p>Текущий месячный доход: <strong>${result.currentIncome} руб.</strong></p>
                <p>Доход с группового объема: <strong>${result.groupIncome} руб.</strong></p>
                <p>Бонус наставника: <strong>${result.mentorIncome} руб.</strong></p>
        `;
        
        if (result.isPremium) {
            html += `
                <p>Лидерский бонус: <strong>${result.leaderIncome} руб.</strong></p>
                <p>PRO бонус: <strong>${result.proBonus} руб.</strong></p>
                <p>Общий доход: <strong>${result.totalIncome} руб.</strong></p>
                <hr>
                <h5>Прогноз на 6 месяцев</h5>
                <p>Через 6 месяцев ваш доход может составить: <strong>${result.forecast[6]} руб.</strong></p>
            `;
        }
        
        html += `</div>`;
        
        document.getElementById('results').innerHTML = html;
    } catch (error) {
        console.error('Ошибка:', error);
        alert('Произошла ошибка при расчете');
    }
});
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>