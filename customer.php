<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <link rel="stylesheet" href="order-custstyle.css">
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
        <h2>Customer Management</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>E-mail</th>
                <th>Password</th>
                <th>Total Orders</th>
            </tr>
            <?php
            include 'config.php';
            $query = "SELECT u.id, u.username, u.email, u.password, COUNT(o.user_id) AS total_orders 
                        FROM users u 
                        LEFT JOIN orders o ON u.id = o.user_id 
                        GROUP BY u.id, u.username, u.email, u.password";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['password']; ?></td>
                        <td><?php echo $row['total_orders']; ?></td>
                    </tr>
            <?php }
            } else {
                echo '<tr><td colspan="6">No Customers found.</td></tr>';
            }
            ?>
        </table>
    </div>
    <script src="dashscript.js"></script>
    <script src="orderscript.js"></script>
</body>

</html>