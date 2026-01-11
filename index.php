<?php
require 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$category_id = $_GET['category'] ?? null;
$brand = $_GET['brand'] ?? null;

$brands = [];
if ($category_id) {
    $brands_stmt = $pdo->prepare("SELECT DISTINCT brand FROM products WHERE category_id = ?");
    $brands_stmt->execute([$category_id]);
    $brands = $brands_stmt->fetchAll();
}

$cart_stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
$cart_stmt->execute([$user_id]);
$cart_count = $cart_stmt->fetch()['total_items'] ?? 0;

$query = "SELECT p.*, c.name AS category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1";
$params = [];

if ($category_id) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}
if ($brand) {
    $query .= " AND p.brand = ?";
    $params[] = $brand;
}

$query .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$fav_stmt = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
$fav_stmt->execute([$user_id]);
$favorites = $fav_stmt->fetchAll(PDO::FETCH_COLUMN);

require 'index.html';
