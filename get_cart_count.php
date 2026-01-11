<?php
require 'config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["cart_count" => 0]);
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart_count = $stmt->fetch()['total_items'] ?? 0;

echo json_encode(["cart_count" => (int)$cart_count]);
exit;
?>
