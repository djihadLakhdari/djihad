<?php
require 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$cart_stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
$cart_stmt->execute([$user_id]);
$cart_count = $cart_stmt->fetch()['total_items'] ?? 0;

$query = "SELECT p.*, c.name AS category_name 
          FROM favorites f 
          JOIN products p ON f.product_id = p.id 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE f.user_id = ?
          ORDER BY f.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$favorites_products = $stmt->fetchAll();
require 'favorite.html';
?>
