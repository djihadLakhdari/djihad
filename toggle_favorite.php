<?php
require 'config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["message" => "not_logged_in"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$product_id = $_POST["product_id"] ?? null;

if (!$product_id) {
    echo json_encode(["status" => "error", "message" => "No product id"]);
    exit;
}

try {
  
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $exists = $stmt->fetch();

    if ($exists) {
        $del = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
        $del->execute([$user_id, $product_id]);
        echo json_encode(["status" => "removed"]);
    } else {
        $ins = $pdo->prepare("INSERT INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())");
        $ins->execute([$user_id, $product_id]);
        echo json_encode(["status" => "added"]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
