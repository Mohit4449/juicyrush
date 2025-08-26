<?php
include 'config.php';

$query = "SELECT o.id, o.order_details, o.total_items, o.total_amount, o.date_of_order, u.username 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.id DESC 
          LIMIT 1";
$result = mysqli_query($conn, $query);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

echo json_encode($orders);
?>
