<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="customerstyle.css">
    <script src="dashscript.js" defer></script>
    <title>Admin • Users Page</title>
</head>

<body>
    <div class="flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="logo-container">
                <img src="images/logo.png" alt="logo">
            </div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">Home</a>
                <a href="adproduct.php" class="nav-link <?= ($current_page == 'adproduct.php') ? 'active' : '' ?>">Products</a>
                <a href="admin_custom_juice.php" class="nav-link <?= ($current_page == 'admin_custom_juice.php') ? 'active' : '' ?>">Custom Products</a>
                <a href="order.php" class="nav-link <?= ($current_page == 'order.php') ? 'active' : '' ?>">Orders</a>
                <a href="customer.php" class="nav-link <?= ($current_page == 'customer.php') ? 'active' : '' ?>">Users</a>
                <a href="logout.php" class="nav-link logout">Logout</a>
                <div class="theme-toggle">
                    <input type="checkbox" id="themeSwitch" />
                    <label for="themeSwitch" class="toggle">
                        <span class="toggle-icons">
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-moon"></i>
                        </span>
                    </label>
                </div>

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