<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
session_start();

include __DIR__ . '/templates/header.php';
?>

<div class="container">
    <header class="text-center my-5 p-4 bg-success text-white rounded">
        <h1>Greenway Business Calculator 2025</h1>
        <p class="lead">Точный расчет доходов по новому маркетинг-плану</p>
    </header>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h3>Бесплатный калькулятор</h3>
                </div>
                <div class="card-body">
                    <p>Рассчитайте свой потенциальный доход в Greenway с помощью нашего калькулятора.</p>
                    <a href="login.php" class="btn btn-success">Начать расчет</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h3>Премиум доступ</h3>
                </div>
                <div class="card-body">
                    <p>Получите детализированный бизнес-план и расширенный функционал калькулятора.</p>
                    <a href="payment.php" class="btn btn-warning">Узнать больше</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>