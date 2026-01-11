<?php
require 'config.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>📦 طلباتي</title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f9f9f9;
            direction: rtl;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .status {
            font-weight: bold;
        }

        .pending {
            color: orange;
        }

        .shipped {
            color: blue;
        }

        .delivered {
            color: green;
        }

        .cancelled {
            color: red;
        }
    </style>
</head>
<body>

<h2>📦 قائمة طلباتك</h2>

<?php if (empty($orders)): ?>
    <p style="text-align:center;">🚫 لا توجد طلبات.</p>
<?php else: ?>
    <table>
        <tr>
            <th>رقم الطلب</th>
            <th>المجموع</th>
            <th>التاريخ</th>
            <th>الحالة</th>
        </tr>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= $order["id"] ?></td>
                <td><?= $order["total"] ?> دج</td>
                <td><?= $order["created_at"] ?></td>
                <td class="status <?= strtolower($order["status"]) ?>">
                    <?= htmlspecialchars($order["status"]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>