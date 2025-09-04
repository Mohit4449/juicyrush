<?php
session_start();
include('config.php');

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']) && isset($_SESSION['username']);

// Only set $userId if logged in
$userId = $loggedIn ? $_SESSION['user_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">

  <title>My Account - Juicy Rush</title>
  <link rel="stylesheet" href="style/myaccstyle.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <div class="account-page-container">

    <!-- Navbar -->
    <header class="navbar">
    <div class="navbar-container">
      <div class="logo">
        <a href="home.php"><img src="images/logo-removebg-preview.png" alt="Juice Logo"></a>
      </div>

      <button class="menu-toggle" id="menu-toggle">
        <i class="fas fa-bars"></i>
      </button>

      <nav class="nav-links" id="nav-links">
        <?php if (isset($_SESSION['username'])): ?>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
        <?php endif; ?>
        <a href="home.php">Home</a>
        <a href="product.php">Product</a>
        <a href="about.php">About us</a>
        <a href="contact.php">Contact</a>
      </nav>

      <div class="nav-right">
        <a href="<?php echo isset($_SESSION['username']) ? 'myacc.php' : 'login.php'; ?>" class="user-icon">
          <i class="fas fa-user-circle"></i>
        </a>
        <a href="product.php" class="shop-btn">Shop Now</a>
      </div>
    </div>
  </header>


    <!-- Main Account Section -->

    <section class="account-section">
      <h1>My Account</h1>

      <?php if ($loggedIn): ?>
        <p class="loggedin">
          Logged in as <?= htmlspecialchars($_SESSION['username']) ?> / <a href="logout.php">Log out</a>
        </p>
        <h2>Your Order</h2>
        <?php
        
        $sql = "SELECT o.id, u.username, o.order_details, o.total_items, o.total_amount, o.date_of_order
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.user_id = ?
        ORDER BY o.date_of_order DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0): ?>
          <div class="order-table-container">
            <table class="order-table">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Username</th>
                  <th>Order Details</th>
                  <th>Total Items</th>
                  <th>Total Amount</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td>
                      <?php
                      $items = json_decode($row['order_details'], true);
                      if (is_array($items)) {
                        foreach ($items as $item) {
                          echo htmlspecialchars($item['name']) . " (₹" . htmlspecialchars($item['price']) . ")<br>";
                        }
                      } else {
                        echo "Invalid format";
                      }
                      ?>
                    </td>
                    <td><?= htmlspecialchars($row['total_items']) ?></td>
                    <td>₹<?= htmlspecialchars($row['total_amount']) ?></td>
                    <td><?= htmlspecialchars($row['date_of_order']) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p>No orders placed yet.</p>
        <?php endif; ?>

        <div class="account-box">
          <h3>Account Details</h3>
          <a href="address.php" class="address-btn">View Addresses (0)</a>
        </div>
      <?php else: ?>
        <p class="notlogged">Please <a href="login.php">login</a> to view your account.</p>
      <?php endif; ?>
    </section>

    <!-- Footer Section -->
    <footer class="footer">
      <div class="footer-container">
        <!-- Column 1 -->
        <div class="footer-column logo-col">
          <a href="home.php"><img src="images/logo-removebg-preview.png" alt="Juice Logo" class="footer-logo"></a>
          <p class="footer-text">© 2025, Juicy Rush Pvt. Ltd.</p>
        </div>

             <!-- Column 2 -->
      <div class="footer-column">
        <ul>
          <li><a href="product.php">Shop now</a></li>
          <li><a href="myacc.php">Orders</a></li>
          <li><a href="about.php">Know Us</a></li>
        </ul>
      </div>

      <!-- Column 3 -->
      <div class="footer-column">
        <ul>
          <li><a href="about.php">About Us</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="#">Terms & Conditions</a></li>
        </ul>
      </div>

      <!-- Column 4 -->
      <div class="footer-column">
        <ul>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Shipping Policy</a></li>
          <li><a href="#">Return Policy</a></li>
        </ul>
      </div>

        <!-- Column 4: Social Icons (Updated) -->
        <div class="footer-column social">
          <ul class="footer-social-icons">
            <li class="footer-icon-content">
              <a href="#" aria-label="X" data-social="x">
                <div class="footer-filled"></div>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z" />
                </svg>
              </a>
            </li>
            <li class="footer-icon-content">
              <a href="#" aria-label="Instagram" data-social="instagram">
                <div class="footer-filled"></div>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
                </svg>
              </a>
            </li>
            <li class="footer-icon-content">
              <a href="#" aria-label="YouTube" data-social="youtube">
                <div class="footer-filled"></div>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z" />
                </svg>
              </a>
            </li>
            <li class="footer-icon-content">
              <a href="#" aria-label="Email" data-social="email">
                <div class="footer-filled"></div>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                </svg>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </footer>
  </div>


</body>

</html>