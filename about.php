<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Fresh Juice</title>
    <link rel="stylesheet" href="style/aboutstyle.css">
    <!-- Add Animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
    <!-- Navigation Bar -->
    <?php
    session_start();
    ?>
    <header class="navbar">
        <div class="navbar-container">
            <nav class="nav-links">
                <a href="index.php" class="animate__animated animate__fadeIn">Home</a>
                <a href="<?php echo isset($_SESSION['username']) ? 'product.php' : 'login.php'; ?>" class="animate__animated animate__fadeIn">Product</a>
                <a href="about.php" class="animate__animated animate__fadeIn">About us</a>
                <a href="contact.php" class="animate__animated animate__fadeIn">Contact</a>
            </nav>
            <div class="logo animate__animated animate__fadeIn">
                <a href="index.php"><img src="images/logo-removebg-preview.png" alt="Juice Logo"></a>
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section class="about animate__animated animate__fadeInUp">
        <div class="about-content">
            <h2>Our Essence</h2>
            <p>Fresh Juice is not just a drink; it's an experience. We bring nature's best to your glass, crafted with care, innovation, and passion.</p>
        </div>
        <div class="about-image">
            <img src="images/homiuce02-1024x684.jpg" alt="A refreshing glass of juice with fresh fruits">
        </div>
    </section>

    <!-- Juice Gallery Section -->
    <section class="juice-gallery animate__animated animate__fadeInUp">
        <h2>Our Delicious Juices</h2>
        <div class="gallery">
            <img src="images/fruitbottle7.png" alt="Pineapple Juice" class="animate__animated animate__zoomIn">
            <img src="images/fruitbottle1.png" alt="Lemon juice" class="animate__animated animate__zoomIn">
            <img src="images/fruitbottle3.png" alt="Strawberry Juice" class="animate__animated animate__zoomIn">
            <img src="images/fruitbottle6.png" alt="Kiwi Juice" class="animate__animated animate__zoomIn">
        </div>
    </section>

    <!-- Delivery Policies Section -->
    <section class="delivery-policies animate__animated animate__fadeInUp">
        <h2>Delivery Policies</h2>
        <div class="policies">
            <div class="policy">
                <h3>Fast Delivery</h3>
                <p>We deliver your favorite juices within 20 minutes of ordering. Freshness guaranteed!</p>
            </div>
            <div class="policy">
                <h3>Free Shipping</h3>
                <p>Enjoy free shipping on all orders above ₹700. Delivered straight to your doorstep.</p>
            </div>
            <div class="policy">
                <h3>Eco-Friendly Packaging</h3>
                <p>We use 100% recyclable materials to ensure sustainability.</p>
            </div>
        </div>
    </section>

    <!-- Technology Section -->
    <section class="technology animate__animated animate__fadeInUp">
        <h2>Advanced Technology</h2>
        <p>We use state-of-the-art cold-press extraction and AI-powered quality control to ensure maximum nutrition and flavor in every sip.</p>
        <p>Our smart IoT monitoring system maintains optimal temperature and freshness throughout the supply chain.</p>
    </section>
    <section class="mission animate__animated animate__fadeInUp">
        <div class="mission-container">
            <h2>Our Purpose</h2>
            <p>We strive to blend the finest fruits into delightful beverages that fuel your health, energize your day, and refresh your spirit.</p>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values animate__animated animate__fadeInUp">
        <div class="values-container">
            <h2>Our Core Values</h2>
            <ul>
                <li><strong>Purity:</strong> No additives, just real fruit.</li>
                <li><strong>Innovation:</strong> Unique flavors crafted with love.</li>
                <li><strong>Sustainability:</strong> Eco-friendly practices in every sip.</li>
            </ul>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer animate__animated animate__fadeInUp">
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
</body>

</html>