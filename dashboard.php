<?php
include 'config.php';

// ================= Revenue Data =================
$query = "
    SELECT 
        DATE_FORMAT(date_of_order, '%Y-%m') AS month,
        SUM(total_amount) AS revenue
    FROM orders
    GROUP BY DATE_FORMAT(date_of_order, '%Y-%m')
    ORDER BY month ASC
";
$result = mysqli_query($conn, $query);

$months = [];
$revenues = [];

while ($row = mysqli_fetch_assoc($result)) {
    $months[] = $row['month'];
    $revenues[] = $row['revenue'];
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
                $name = $product['name']; // product name
                if (!isset($productCounts[$name])) {
                    $productCounts[$name] = 0;
                }
                $productCounts[$name] += 1; // count each occurrence
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

    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
                        $query = "SELECT SUM(total_amount) AS total_revenue FROM orders";
                        $result = mysqli_query($conn, $query);
                        $row = mysqli_fetch_assoc($result);
                        echo '₹ ' . number_format($row['total_revenue']);
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
        // === Revenue Chart ===
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($revenues); ?>,
                    backgroundColor: 'rgba(26, 66, 66, 0.58)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                }]
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
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        // === Most Sold Products Chart ===
        const productCtx = document.getElementById('productChart').getContext('2d');
        new Chart(productCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $productLabels; ?>,
                datasets: [{
                    label: 'Units Sold',
                    data: <?php echo $productData; ?>,
                    backgroundColor: [
                        '#FF6384', '#FFCE56', '#5fea90ff',
                        '#ff8a04ff', '#7735fbff', '#FF9F40', '#00ff40ff'
                    ],
                    borderWidth: 1
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
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

</body>

</html>