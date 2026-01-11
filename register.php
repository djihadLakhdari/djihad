<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $birth_date = $_POST["birth_date"];
    $gender = $_POST["gender"];

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, birth_date, gender) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $password, $birth_date, $gender]);

    header("Location: login.php");
}
require 'register.html';
?>
