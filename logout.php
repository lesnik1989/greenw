<?php
require_once __DIR__ . '/includes/config.php';
session_start();

session_unset();
session_destroy();

header("Location: " . SITE_URL . "/login.php");
exit;
?>