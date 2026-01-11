<?php
require 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    echo "not_logged_in";
    exit;
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = (int)$_POST["product_id"];

    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);

    echo "deleted";
    exit;
}
?>
