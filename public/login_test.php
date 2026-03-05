<?php
require_once __DIR__ . '/../config/config.php';
session_start();
$_SESSION['user_id'] = 2; // Assuming a customer ID
$_SESSION['role'] = 'customer';
$_SESSION['full_name'] = 'Test Customer';
$_SESSION['avatar'] = '';
header('Location: ' . APP_URL . '/orders');
exit;
