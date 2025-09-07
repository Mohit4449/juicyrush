<?php
include 'config.php';

// detect current page
$current_page = basename($_SERVER['PHP_SELF']);

// ================= Revenue Data =================
// Revenue from normal orders
$query1 = "
    SELECT 
        DATE_FORMAT(date_of_order, '%Y-%m') AS month,
        SUM(total_amount) AS revenue
    FROM orders
    GROUP BY DATE_FORMAT(date_of_order, '%Y-%m')
    ORDER BY month ASC
";
$result1 = mysqli_query($conn, $query1);

$months = [];
$normalRevenue = [];
while ($row = mysqli_fetch_assoc($result1)) {
    $months[] = $row['month'];
    $normalRevenue[] = $row['revenue'];
}

// Revenue from custom orders
$query2 = "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS month,
        SUM(total_amount) AS revenue
    FROM custom_order
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC
";
$result2 = mysqli_query($conn, $query2);

$customRevenue = [];
$customMonths = [];
while ($row = mysqli_fetch_assoc($result2)) {
    $customMonths[] = $row['month'];
    $customRevenue[] = $row['revenue'];
}

// ✅ Align months
$allMonths = array_unique(array_merge($months, $customMonths));
sort($allMonths);

$normalRevenueAligned = [];
$customRevenueAligned = [];
foreach ($allMonths as $m) {
    $normalRevenueAligned[] = in_array($m, $months) ? $normalRevenue[array_search($m, $months)] : 0;
    $customRevenueAligned[] = in_array($m, $customMonths) ? $customRevenue[array_search($m, $customMonths)] : 0;
}

// ================= Most Sold Products =================
$sql = "SELECT order_details FROM orders";
$result = $conn->query($sql);

$productCounts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderDetails = json_decode($row['order_details'], true);
        if (is_array($orderDetails)) {
            foreach ($orderDetails as $product) {
                $name = $product['name'];
                if (!isset($productCounts[$name])) {
                    $productCounts[$name] = 0;
                }
                $productCounts[$name] += 1;
            }
        }
    }
}

$productLabels = json_encode(array_keys($productCounts));
$productData   = json_encode(array_values($productCounts));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <title>Admin • Dashboard</title>
    <link rel="stylesheet" href="dashstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>
    <script src="dashscript.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            <!-- Cards -->
            <div class="grid-container">
                <div class="card">
                    <a href="adproduct.php">
                        <h2 class="card-title">Total Products</h2>
                    </a>
                    <p class="card-value">
                        <?php
                        $query = "SELECT COUNT(*) AS total_products FROM products";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total_products'];
                        ?>
                    <div class="icon">
                        <i class="fi fi-rr-milk-alt"></i>
                    </div>
                    </p>
                </div>

                <div class="card">
                    <h2 class="card-title">Total Revenue</h2>
                    <p class="card-value">
                        <?php
                        // Total revenue from normal orders
                        $query1 = "SELECT SUM(total_amount) AS total_revenue FROM orders";
                        $result1 = mysqli_query($conn, $query1);
                        $row1 = mysqli_fetch_assoc($result1);
                        $normal_revenue = $row1['total_revenue'] ?? 0;

                        // Total revenue from custom orders
                        $query2 = "SELECT SUM(total_amount) AS total_revenue FROM custom_order";
                        $result2 = mysqli_query($conn, $query2);
                        $row2 = mysqli_fetch_assoc($result2);
                        $custom_revenue = $row2['total_revenue'] ?? 0;

                        // Combine both
                        $total_revenue = $normal_revenue + $custom_revenue;

                        echo '₹ ' . number_format($total_revenue, 2);
                        ?>
                    <div class="icon">
                        <i class="fi fi-rs-coins"></i>
                    </div>
                    </p>

                </div>

                <div class="card">
                    <a href="order.php">
                        <h2 class="card-title">Total Orders</h2>
                    </a>
                    <p class="card-value">
                        <?php
                        $query = "SELECT COUNT(*) AS total_orders FROM orders";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total_orders'];
                        ?>
                    <div class="icon">
                        <i class="fi fi-rs-order-history"></i>
                    </div>

                    </p>
                </div>

                <div class="card">
                    <h2 class="card-title">Total Customers</h2>

                    <p class="card-value">
                        <?php
                        $query = "SELECT COUNT(*) AS total_cust FROM users";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        echo $row['total_cust'];
                        ?>
                    <div class="icon">
                        <i class="fi fi-rs-users"></i>
                    </div>

                    </p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <!-- Revenue Chart -->
                <div class="chart-card">
                    <h2 class="chart-title">Revenue</h2>
                    <canvas id="revenueChart"></canvas>
                </div>

                <!-- Most Sold Products Chart -->
                <div class="chart-card">
                    <h2 class="chart-title">Most Sold Products</h2>
                    <canvas id="productChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($allMonths); ?>,
                datasets: [{
                        label: 'Normal Orders Revenue',
                        data: <?php echo json_encode($normalRevenueAligned); ?>,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Custom Orders Revenue',
                        data: <?php echo json_encode($customRevenueAligned); ?>,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                devicePixelRatio: 2,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Revenue (₹)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: "#1f2937" // default light mode
                        }
                    }
                }
            }
        });

        // === Most Sold Products Chart ===
        const productCtx = document.getElementById('productChart').getContext('2d');
        const productChart = new Chart(productCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $productLabels; ?>,
                datasets: [{
                    label: 'Units Sold',
                    data: <?php echo $productData; ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)', // pink
                        'rgba(255, 206, 86, 0.6)', // yellow
                        'rgba(95, 234, 144, 0.6)', // green
                        'rgba(255, 138, 4, 0.6)', // orange
                        'rgba(119, 53, 251, 0.6)', // purple
                        'rgba(255, 159, 64, 0.6)', // orange-light
                        'rgba(0, 255, 64, 0.6)' // neon green
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(95, 234, 144, 1)',
                        'rgba(255, 138, 4, 1)',
                        'rgba(119, 53, 251, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(0, 255, 64, 1)'
                    ],
                    borderWidth: 2,
                    barPercentage: 0.8

                }]

            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        ticks: {
                            color: "#1f2937"
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                        labels: {
                            color: "#1f2937"
                        }
                    }
                }
            }
        });
    </script>


</body>

</html>