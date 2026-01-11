<?php
require 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    $product_id = (int)$_POST["product_id"];
    $qty = max(1, (int)$_POST["quantity"]);
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$qty, $user_id, $product_id]);
    header("Location: cart.php?updated=1");
    exit;
}

if (isset($_GET["remove"])) {
    $pid = (int)$_GET["remove"];
    $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?")->execute([$user_id, $pid]);
    header("Location: cart.php?removed=1");
    exit;
}

$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.image, p.price, c.quantity 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

$total = 0;
require 'cart.html'; 
?>

