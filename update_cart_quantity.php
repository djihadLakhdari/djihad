<?php
require 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}
if (!isset($_POST['product_id'], $_POST['quantity'])) {
    http_response_code(400);
    exit;
}
$user_id = $_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];
$quantity = max(1, (int)$_POST['quantity']);

$pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?")
    ->execute([$quantity, $user_id, $product_id]);
echo "OK";