<?php
session_start();
// Redirect admin users to the admin dashboard
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Juicy Rush - Home</title>
  <link rel="stylesheet" href="style/homestyles.css">
</head>
<body>
  <header class="navbar">
    <div class="navbar-container">
        <nav class="nav-links">
            <?php if (isset($_SESSION['username'])): ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
            <a href="index.php">Home</a>
            <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>">Product</a>
            <a href="about.php">About us</a>
            <a href="contact.php">Contact</a>
        </nav>
        <div class="logo">
            <a href="index.php"><img src="images/logo-removebg-preview.png" alt="Juice Logo"></a>
        </div>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
              
    </div>
  </header>

  <!--background image-->
  <section class="bgimg">
    <div class="poster">
      <img src="images/bg.png" alt="Juice Logo">
    </div>
  </section>
  <!--hero section-->
  <section class="hero">
    <div class="hero-content">
      <h1>From Farm to Bottle, Freshness Delivered.</h1>
      <img src="images/bottels.png" alt="Juice Bottles" class="hero-image">
      <div class="features">
        <div class="feature">
          <img src="images/icon1.png" alt="No Sugar">
          <p>No Sugar</p>
        </div>
        <div class="feature">
          <img src="images/icon2.png" alt="20G Protein">
          <p>20G Protein</p>
        </div>
        <div class="feature">
          <img src="images/icon3.png" alt="Naturally Occurring">
          <p>Naturally Occurring</p>
        </div>
        <div class="feature">
          <img src="images/icon4.png" alt="100% Pure">
          <p>100% Pure</p>
        </div>
        <div class="feature">
          <img src="images/icon5.png" alt="Keto-Friendly">
          <p>Keto-Friendly</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Products Section -->

  <section class="product-section">
    <h2>Our Products</h2>
    <div class="product-banner">
      <img src="images/orangebg.jpg" alt="Orange Slices">
    </div>

    <div class="product-grid">
      <div class="product-card">
        <img src="images/js7.png" alt="Juice Lemon">
        <h3>Citrus Splash</h3>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
      </div>
      <div class="product-card">
        <img src="images/js6.png  " alt="Juice Kiwi">
        <h3>Green Detox</h3>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
      </div>
      <div class="product-card">
        <img src="images/js3.png" alt="Juice Orange">
        <h3>Orange Bliss</h3>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
      </div>
      <div class="product-card">
        <img src="images/js1.png" alt="Juice Strawberry">
        <h3>Watermelon Chill</h3>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
      </div>
      <div class="product-card">
        <img src="images/js5.png" alt="Juice mango">
        <h3>berry Brust</h3>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
      </div>
      <div class="product-card">
        <img src="images/js8.png" alt="Juice pomegranate">
        <h3>Pomegranate Power</h3>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
      </div>
      <div class="product-card">
        <img src="images/js2.png" alt="Juice Pineapple">
        <h3>Tropical Punch</h3>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
      </div>
      <div class="product-card">
        <img src="images/js4.png" alt="Juice Avocado">
        <h3>Apple Zing</h3>
        <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="shop-btn">Shop Now</a>
      </div>
    </div>
  </section>

  <!-- About Section -->
<section class="about-section">
  <div class="about-container">
    <div class="about-content">
      <h2>About Juicy Rush</h2>
      <p>
        At Juicy Rush, we believe in delivering the freshest and most nutritious juices straight from the farm to your bottle. 
        Our juices are made from 100% natural ingredients, with no added sugars or artificial flavors. 
        We source our fruits from local farmers who share our commitment to quality and sustainability.
      </p>
      <p>
        Whether you're looking for a refreshing drink, a healthy snack, or a boost of energy, our juices are crafted to 
        meet your needs. From classic flavors like Orange and Lemon to exotic blends like Kiwi and Avocado, 
        we have something for everyone. Join us on our journey to promote health and wellness, one sip at a time!
      </p>
    </div>
    <div class="about-image">
      <img src="images/aboutpic.jpg" alt="About Juicy Rush">
    </div>
  </div>
</section>

  <!-- Footer Section -->
  <footer class="footer">
    <div class="footer-container">
      <img src="images/logo-removebg-preview.png" alt="Juice Logo" class="footer-logo">
      <nav class="footer-nav">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="product.php">Products</a></li>
          <li><a href="about.php">About us</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </nav>
      <p class="footer-text">© 2025 Juice Company. All Rights Reserved.</p>
    </div>
  </footer>

  <script>
        const toggleButton = document.getElementById('menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        toggleButton.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    </script>

</body>

</html>