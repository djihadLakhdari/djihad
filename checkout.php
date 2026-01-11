<?php
require 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT p.id, p.name, p.image, p.price, c.quantity 
                       FROM cart c JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

if (empty($items)) {
    echo "<h2 style='text-align:center;'>🚫 السلة فارغة.</h2>";
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $wilaya  = $_POST["wilaya"];
    $commune = $_POST["commune"];
    $address = $_POST["address"];
    $phone   = $_POST["phone"];
    $payment = $_POST["payment"];

    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, wilaya, commune, address, phone, payment_method, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, 'قيد المعالجة')");
    $stmt->execute([$user_id, $total, $wilaya, $commune, $address, $phone, $payment]);
    $order_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items as $item) {
        $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
    }

    $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user_id]);


    echo "<h2 style='text-align:center; color:green;'>✅ تم تنفيذ الطلب بنجاح!</h2>";
    echo "<p style='text-align:center;'>رقم الطلب: $order_id<br>المجموع: $total دج</p>";

    echo "<h3 style='text-align:center;'>🛒 محتوى الطلب</h3>";
    echo "<table style='margin:auto; border-collapse: collapse; width: 90%;'>";
    echo "<thead><tr>
            <th style='border:1px solid #ccc; padding:8px;'>المنتج</th>
            <th style='border:1px solid #ccc; padding:8px;'>الكمية</th>
            <th style='border:1px solid #ccc; padding:8px;'>السعر لكل وحدة</th>
            <th style='border:1px solid #ccc; padding:8px;'>المجموع</th>
          </tr></thead>";
    echo "<tbody>";
    foreach ($items as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        echo "<tr>";
        echo "<td style='border:1px solid #ccc; padding:8px;'>" . htmlspecialchars($item['name']) . "</td>";
        echo "<td style='border:1px solid #ccc; padding:8px; text-align:center;'>" . $item['quantity'] . "</td>";
        echo "<td style='border:1px solid #ccc; padding:8px; text-align:right;'>" . number_format($item['price'], 2) . " دج</td>";
        echo "<td style='border:1px solid #ccc; padding:8px; text-align:right;'>" . number_format($subtotal, 2) . " دج</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";

   
    echo "<div style='text-align:center; margin-top:20px;'>";
    echo "<button onclick='window.print()' style='padding:10px 20px; background:#2196F3; color:white; border:none; border-radius:8px; cursor:pointer;'>🖨️ طباعة الفاتورة</button>";
    echo "</div>";

    echo "<div style='text-align:center; margin-top:20px;'>";
    echo "<a href='index.php' style='padding:10px 20px; background:#4CAF50; color:white; border-radius:10px; text-decoration:none;'>🔙 العودة للرئيسية</a>";
    echo "</div>";

    exit;
}
require 'checkout.html';
?>