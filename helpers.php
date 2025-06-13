<?php
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function generateActivationKey() {
    return bin2hex(random_bytes(16));
}

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function generatePaymentQRData($plan, $details) {
    return "ST00012|Name=Greenway Premium {$plan['name']}|PersonalAcc={$details['account_number']}|BankName={$details['bank_name']}|BIC=044525225|CorrespAcc=30101810400000000225|PayeeINN={$details['recipient_inn']}|Sum={$plan['price']}|Purpose=Оплата подписки {$plan['name']}";
}
?>