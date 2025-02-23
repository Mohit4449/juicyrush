<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juicy rush</title>
    <link rel="stylesheet" href="style/productstyle.css">
</head>

<body>
    <!-- navigation bar -->
    <header class="navbar">
        <div class="navbar-container">
            <nav class="nav-links">
                <a href="home.php">Home</a>
                <a href="product.php">Product</a>   
                <a href="about.php">About us</a>
                <a href="contact.php">Contact</a>
            </nav>

            <div class="logo">
                <a href="home.php"><img src="images/logo-removebg-preview.png" alt="Juice Logo"></a>
            </div>

            <!-- Cart Icon -->
            <div class="cart-icon" id="cartIcon">
                <span class="cart-count" id="cartCount">0</span>
                <img src="images/cart-add-solid-36.png" alt="cart">
            </div>
        </div>
    </header>

    <!-- Cart Slider -->
    <div class="cart-slider" id="cartSlider">
        <div class="cart-header">
            <h3>Your Cart</h3>
            <button class="close-cart" id="closeCart">&times;</button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Cart items will be dynamically added here -->
        </div>
        <div class="cart-total">
            <h4>Total: $<span id="cartTotal">0.00</span></h4>
        </div>
        <button class="checkout-btn" id="checkoutBtn">Proceed to Payment</button>
    </div>

    <!-- Product Section -->
    <section class="product-section">
        <h2>Our Juices</h2>
        <div class="product-container">
            <?php
            $conn = new mysqli('localhost', 'root', '', 'dbjuice');
            if ($conn->connect_error) {
                die('Connection failed: ' . $conn->connect_error);
            }
            $result = $conn->query("SELECT * FROM products");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <div class="product-card">
                        <div class="product-card-content">
                        <img src="<?php echo $row['image_path']; ?>" alt="<?php echo $row['name']; ?>" class="product-image">

                            <h3><?php echo $row['name']; ?></h3>
                            <p><?php echo $row['description']; ?></p>
                            <span class="price">₹ <?php echo $row['price']; ?></span>
                        </div>
                        <div class="product-card-drawer">
                            <h3><?php echo $row['name']; ?></h3>
                            <p>Ingredients: <?php echo $row['ingredients']; ?></p>
                            <button class="add-to-cart">Add to Cart</button>
                        </div>
                    </div>
                <?php }
            } else {
                echo "<p>No products available at the moment.</p>";
            }
            $conn->close();
            ?>
        </div>
        
        
    </section>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-container">
            <img src="images/logo-removebg-preview.png" alt="Juice Logo" class="footer-logo">
            <nav class="footer-nav">
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="product.php">Products</a></li>
                    <li><a href="about.php">About us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
            <p class="footer-text">© 2025 Juice Company. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Script for Animations and Cart Functionality -->
    <script src="productscript.js"></script>
</body>

</html>