<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="orderstyle.css">
    <script src="dashscript.js" defer></script>
    <title>Orders Page</title>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="images/logo-removebg-preview.png" alt="Juice Logo" class="logo">
        </div>
        <nav class="nav-links">
            <a href="dashboard.php" class="nav-link">Home</a>
            <a href="adproduct.php" class="nav-link">Products</a>
            <a href="order.php" class="nav-link">Orders</a>
            <a href="customer.php" class="nav-link">Users</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <button class="openbtn" onclick="toggleSidebar()">☰ Menu</button>
        <h2>Order Management</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Order Details</th>
                <th>Total Items</th>
                <th>Total Amount</th>
                <th>Date of Order</th>
            </tr>
            <?php
            include 'config.php';
            $query = "SELECT u.id, u.username, o.order_details, o.total_items, o.total_amount, o.date_of_order 
                      FROM orders o 
                      JOIN users u ON u.id = o.user_id
                      ORDER BY o.date_of_order DESC ";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {

                    $tmp_arr = json_decode($row['order_details'], true);

            ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php
                            $tmp_arr = json_decode($row['order_details'], true); // Decode as an associative array

                            if (!empty($tmp_arr) && is_array($tmp_arr)) {
                                foreach ($tmp_arr as $item) {
                                    echo isset($item['name']) ? $item['name'] . '<br>' : 'Unnamed Item<br>';
                                }
                            } else {
                                echo "No items available";
                            }
                            ?></td>
                        <td><?php echo $row['total_items']; ?></td>
                        <td><?php echo $row['total_amount']; ?></td>
                        <td><?php echo $row['date_of_order']; ?></td>
                    </tr>
            <?php }
            } else {
                echo '<tr><td colspan="6">No orders found.</td></tr>';
            }
            ?>
        </table>
    </div>
    <script src="dashscript.js"></script>
    <script src="orderscript.js"></script>
</body>

</html>