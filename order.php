<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">

    <link rel="stylesheet" href="orderstyle.css">
    <script src="dashscript.js" defer></script>

    <title>Admin • Orders Page</title>
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
            <h2>Order Management</h2>

            <!-- Buttons to toggle -->
            <div class="order-buttons" role="tablist" aria-label="Order Views">
                <button id="btnNormal" class="toggle-btn active" data-target="normal">Normal Orders</button>
                <button id="btnCustom" class="toggle-btn" data-target="custom">Custom Orders</button>
            </div>

            <!-- Slider viewport -->
            <div class="slider">
                <div class="tables-container" id="tablesContainer">

                    <!-- --- Normal Orders Pane --- -->
                    <div class="pane" id="normalPane" role="tabpanel" aria-labelledby="btnNormal">
                        <div class="table-card">
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Order Details</th>
                                        <th>Total Items</th>
                                        <th>Total Amount</th>
                                        <th>Date of Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'config.php';
                                    // normal orders: ensure we select order id (o.id) as order_id so echo is correct
                                    $query = "SELECT o.id AS order_id, u.username, o.order_details, o.total_items, o.total_amount, o.date_of_order 
          FROM orders o 
          JOIN users u ON u.id = o.user_id
          ORDER BY o.date_of_order DESC";
                                    $result = mysqli_query($conn, $query);
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $items = json_decode($row['order_details'], true);
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['order_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                            echo "<td>";
                                            if (is_array($items) && count($items) > 0) {
                                                foreach ($items as $item) {
                                                    $name = htmlspecialchars($item['name'] ?? 'Unnamed Item');
                                                    $package = htmlspecialchars($item['package'] ?? ($item['package_name'] ?? 'N/A'));
                                                    // prefer 'price', fallback to 'amount' or display dash
                                                    if (isset($item['price'])) {
                                                        $priceText = "₹" . number_format((float)$item['price'], 2);
                                                    } elseif (isset($item['amount'])) {
                                                        $priceText = "₹" . number_format((float)$item['amount'], 2);
                                                    } else {
                                                        $priceText = "-";
                                                    }
                                                    echo "{$name} ({$package}) - {$priceText}<br>";
                                                }
                                            } else {
                                                echo "No items";
                                            }
                                            echo "</td>";
                                            echo "<td>" . htmlspecialchars($row['total_items']) . "</td>";
                                            echo "<td>₹" . htmlspecialchars(number_format((float)$row['total_amount'], 2)) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['date_of_order']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo '<tr><td colspan="6">No normal orders found.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- --- Custom Orders Pane --- -->
                    <div class="pane" id="customPane" role="tabpanel" aria-labelledby="btnCustom">
                        <div class="table-card">
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Custom Mix</th>
                                        <th>Total Volume</th>
                                        <th>Total Amount</th>
                                        <th>Date of Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // custom orders: query the custom_order table (adjust names if your table differs)
                                    $query2 = "SELECT co.id AS order_id, u.username, co.fruits, co.ingredients, co.total_volume_ml, co.total_amount, co.created_at
           FROM custom_order co
           JOIN users u ON u.id = co.user_id
           ORDER BY co.created_at DESC";
                                    $result2 = mysqli_query($conn, $query2);
                                    if ($result2 && mysqli_num_rows($result2) > 0) {
                                        while ($r = mysqli_fetch_assoc($result2)) {
                                            $fruits = json_decode($r['fruits'], true);
                                            $ings = json_decode($r['ingredients'], true);

                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($r['order_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($r['username']) . "</td>";

                                            // custom mix column: show fruits + ingredients
                                            echo "<td>";
                                            if (is_array($fruits) && count($fruits) > 0) {
                                                foreach ($fruits as $f) {
                                                    $fname = htmlspecialchars($f['name'] ?? 'Unknown');
                                                    // price could be 'price' or 'per100ml' — compute display price if possible
                                                    if (isset($f['price'])) {
                                                        $fprice = "₹" . number_format((float)$f['price'], 2);
                                                    } elseif (isset($f['per100ml']) && !empty($r['total_volume_ml'])) {
                                                        $fprice = "₹" . number_format((float)$f['per100ml'] * ($r['total_volume_ml'] / 100), 2);
                                                    } elseif (isset($f['per100ml'])) {
                                                        $fprice = htmlspecialchars($f['per100ml']) . " per 100ml";
                                                    } else {
                                                        $fprice = "-";
                                                    }
                                                    echo "{$fname} ({$fprice})<br>";
                                                }
                                            } else {
                                                echo "No fruits";
                                            }

                                            // ingredients
                                            if (is_array($ings) && count($ings) > 0) {
                                                echo "<hr style='margin:6px 0;border:none;border-top:1px dashed #eee;'>";
                                                foreach ($ings as $ig) {
                                                    $in = htmlspecialchars($ig['name'] ?? 'Unknown');
                                                    $ip = isset($ig['price']) ? "₹" . number_format((float)$ig['price'], 2) : "-";
                                                    echo "{$in} ({$ip})<br>";
                                                }
                                            }
                                            echo "</td>";

                                            echo "<td>" . htmlspecialchars($r['total_volume_ml']) . " ml</td>";
                                            echo "<td>₹" . htmlspecialchars(number_format((float)$r['total_amount'], 2)) . "</td>";
                                            echo "<td>" . htmlspecialchars($r['created_at']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo '<tr><td colspan="6">No custom orders found.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> <!-- end tables-container -->
            </div> <!-- end slider -->
        </div> <!-- end main-content -->

        <script src="orderscript.js"></script>
        <script>
            // initialize visible pane on load
            document.addEventListener('DOMContentLoaded', function() {
                showTable('normal');
            });

            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('collapsed');
            }
        </script>
        <script>
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        </script>
</body>

</html>