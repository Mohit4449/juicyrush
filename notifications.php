<?php
include 'config.php';

// Fetch unseen orders
$sql = "SELECT id, user_id, order_details, date_of_order 
        FROM orders 
        WHERE seen = 0 
        ORDER BY date_of_order DESC";
$result = mysqli_query($conn, $sql);

$notifications = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }

    // Mark them as seen
    $ids = array_column($notifications, 'id');
    $ids_list = implode(',', $ids);
    mysqli_query($conn, "UPDATE orders SET seen = 1 WHERE id IN ($ids_list)");
}

echo json_encode($notifications);
?>
