<?php
require 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
if (isset($_GET['id'])) {
    $pid = (int)$_GET['id'];
    $stmt = $pdo->prepare("INSERT INTO views (user_id, product_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE view_date = CURRENT_TIMESTAMP()");
    $stmt->execute([$user_id, $pid]);
    header("Location: product.php?id=$pid");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
