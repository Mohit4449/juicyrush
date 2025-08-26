<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>
    <script src="dashscript.js" defer></script>
</head>

<body>
    <div class="flex">
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
        <div class="main-content">
            <button class="menu-button" onclick="toggleSidebar()">☰ Menu</button>
            <!-- Notification Bell -->
            <div class="notification-wrapper">
                <div class="bell" id="notificationBell">
                    🔔
                    <span class="badge" id="badgeCount" style="display:none;">0</span>
                </div>
            </div>
            <!-- Notification Dropdown -->
            <div class="notification-dropdown" id="notificationDropdown" style="display:none;">
                <h3>Recent Orders</h3>
                <ul id="orderList"></ul>
            </div>

            <!-- Toasts -->
            <div id="toastContainer"></div>

            <div class="grid-container">
                <!-- Cards -->
                <div class="card">
                    <a href="adproduct.php">
                        <h2 class="card-title">Products</h2>
                    </a>
                    <p class="card-value">
                        <?php
                        include 'config.php';
                        $query = "SELECT COUNT(*) AS total_products FROM products";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total_products'];
                        ?>
                    </p>
                </div>
                <div class="card">
                    <a href="order.php">
                        <h2 class="card-title">Orders</h2>
                    </a>
                    <p class="card-value">
                        <?php
                        include 'config.php';
                        $query = "SELECT COUNT(*) AS total_orders FROM orders";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total_orders'];
                        ?>
                    </p>
                </div>

                <!-- Additional Content -->
                <div class="card">
                    <h2 class="card-title">Customers</h2>
                    <p class="card-value">
                        <?php
                        include 'config.php';
                        $query = "SELECT COUNT(*) AS total_cust FROM users";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total_cust'];
                        ?>
                    </p>
                </div>
                <div class="card">
                    <h2 class="card-title">Revenue</h2>
                    <p class="card-value">
                        <?php
                        include 'config.php';
                        $query = "SELECT SUM(total_amount) AS total_revenue FROM orders";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        echo '₹ ' . number_format($row['total_revenue'], 2);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>