<?php
require 'config.php';
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "not_logged_in"]);
    exit;
}
$user_id = $_SESSION["user_id"];
$product_id = $_POST["product_id"] ?? null;
if (!$product_id) {
    echo json_encode(["status" => "error", "message" => "missing_product_id"]);
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);
$item = $stmt->fetch();
if ($item) {
    $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
    $stmt->execute([$item['id']]);
} else {
   
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$user_id, $product_id]);
}
$cart_stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
$cart_stmt->execute([$user_id]);
$cart_count = $cart_stmt->fetch()['total_items'] ?? 0;

echo json_encode(["status" => "success", "cart_count" => $cart_count]);
exit;
?>
