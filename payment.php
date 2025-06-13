<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/db.php';

session_start();

if (!isAuthenticated()) {
    redirect(SITE_URL . '/login.php');
}

$plan = $_GET['plan'] ?? 'basic';
if (!array_key_exists($plan, $premiumPlans)) {
    $plan = 'basic';
}
$selectedPlan = $premiumPlans[$plan];

include __DIR__ . '/templates/header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3>Оплата премиум доступа: <?= $selectedPlan['name'] ?></h3>
                </div>
                <div class="card-body">
                    <h4>Детали подписки:</h4>
                    <ul>
                        <li>Срок: <?= $selectedPlan['duration'] ?> месяцев</li>
                        <li>Лимит расчетов: <?= $selectedPlan['calculations'] ?></li>
                        <li>Стоимость: <?= $selectedPlan['price'] ?> руб.</li>
                    </ul>
                    
                    <h4>Реквизиты для оплаты:</h4>
                    <div class="payment-details">
                        <p><strong>Банк:</strong> <?= $paymentDetails['bank_name'] ?></p>
                        <p><strong>Номер счета:</strong> <?= $paymentDetails['account_number'] ?></p>
                        <p><strong>Получатель:</strong> <?= $paymentDetails['recipient'] ?></p>
                        <p><strong>ИНН:</strong> <?= $paymentDetails['recipient_inn'] ?></p>
                        <p><strong>Сумма:</strong> <?= $selectedPlan['price'] ?> руб.</p>
                        <p><strong>Назначение платежа:</strong> Премиум подписка Greenway <?= $selectedPlan['name'] ?></p>
                    </div>
                    
                    <div class="text-center my-4">
                        <div id="qrcode"></div>
                        <p>Отсканируйте QR-код для оплаты через мобильный банк</p>
                    </div>
                    
                    <div class="alert alert-info">
                        <p>После оплаты отправьте подтверждение (скриншот или фото чека) на адрес <?= ADMIN_EMAIL ?></p>
                        <p>Мы активируем ваш премиум доступ в течение 24 часов после получения подтверждения.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script>
    // Генерация QR-кода
    const paymentData = `ST00012|Name=Greenway Premium ${'<?= $selectedPlan['name'] ?>'}|PersonalAcc=${'<?= $paymentDetails['account_number'] ?>'}|BankName=${'<?= $paymentDetails['bank_name'] ?>'}|BIC=044525225|CorrespAcc=30101810400000000225|PayeeINN=${'<?= $paymentDetails['recipient_inn'] ?>'}|Sum=${'<?= $selectedPlan['price'] ?>'}|Purpose=Оплата подписки ${'<?= $selectedPlan['name'] ?>'}`;
    
    new QRCode(document.getElementById("qrcode"), {
        text: paymentData,
        width: 200,
        height: 200
    });
</script>

<?php include __DIR__ . '/templates/footer.php'; ?>