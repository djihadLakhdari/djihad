<?php
require 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

if (!isset($_GET['id'])) {
    die("المنتج غير موجود.");
}

$product_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("المنتج غير موجود.");
}

$error = '';

if (isset($_POST['delete_review'])) {
    $review_id = $_POST['review_id'];
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$review_id, $user_id]);
    if ($stmt->rowCount() > 0) {
        $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$review_id]);
        header("Location: product.php?id=$product_id");
        exit;
    } else {
        $error = "⚠️ لا يمكنك حذف تعليق لا يخصك.";
    }
}

if (isset($_POST['edit_review'])) {
    $review_id = $_POST['review_id'];
    $new_rating = $_POST['rating'] ?? null;
    $new_comment = trim($_POST['comment']);

    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
    $stmt->execute([$review_id, $user_id]);
    if ($stmt->rowCount() > 0) {
        if ($new_rating || !empty($new_comment)) {
            $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE id = ?");
            $stmt->execute([$new_rating, $new_comment, $review_id]);
            header("Location: product.php?id=$product_id");
            exit;
        } else {
            $error = "⚠️ يرجى إدخال تقييم أو تعليق لتحديثه.";
        }
    } else {
        $error = "⚠️ لا يمكنك تعديل تعليق لا يخصك.";
    }
}

if (isset($_POST['submit_review'])) {
    $rating = $_POST['rating'] ?? null;
    $comment = trim($_POST['comment']);

    if ($rating || !empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $rating, $comment]);
        header("Location: product.php?id=$product_id");
        exit;
    } else {
        $error = "⚠️ يرجى إدخال تقييم أو تعليق أولًا.";
    }
}


$cart_stmt = $pdo->prepare("SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?");
$cart_stmt->execute([$user_id]);
$cart_count = $cart_stmt->fetch()['total_items'] ?? 0;

$stmt = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll();

$fav_stmt = $pdo->prepare("SELECT product_id FROM favorites WHERE user_id = ?");
$fav_stmt->execute([$user_id]);
$favorites = $fav_stmt->fetchAll(PDO::FETCH_COLUMN);
require 'product.html';
?>