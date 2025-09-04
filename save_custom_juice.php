<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fruits = $_POST['fruits'];
$size = $_POST['size'];

$fruit_names = [];
$total = 0;

foreach ($fruits as $id) {
    $res = mysqli_query($conn, "SELECT name, price FROM fruits WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    $fruit_names[] = $row['name'];
    $total += $row['price'];
}

if ($size == 500) $total += 30;
if ($size == 1000) $total += 60;

$order_details = "Custom Juice: " . implode(", ", $fruit_names) . " ($size ml)";

mysqli_query($conn, "INSERT INTO orders (user_id, order_details, total_amount, date_of_order)
VALUES ('$user_id', '$order_details', '$total', NOW())");

header("Location: product.php");
?>