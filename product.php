<?php
session_start();
include 'config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <title>Juicy rush</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/productstyle.css">
</head>

<body>
    <!-- navigation bar -->
    <header class="navbar">
        <div class="navbar-container">
            <div class="logo">
                <a href="home.php"><img src="images/logo-removebg-preview.png" alt="Juice Logo"></a>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" id="search" placeholder="Search Juices..." autocomplete="off">
                <div id="search-results"></div>
            </div>
            <nav class="nav-links" id="nav-links">
                <a href="home.php">Home</a>
                <a href="product.php">Product</a>
                <a href="about.php">About us</a>
                <a href="contact.php">Contact</a>
            </nav>

            <div class="nav-right">
                <a href="<?php echo isset($_SESSION['username']) ? 'myacc.php' : 'login.php'; ?>" class="user-icon">
                    <i class="fas fa-user-circle"></i>
                </a>
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
            <button class="close-cart" id="closeCart">x</button>
        </div>
        <div class="cart-items" id="cartItems">


            <!-- Cart items will be dynamically added here -->
        </div>
        <div class="cart-total">
            <h4>Total: ₹<span id="cartTotal">0.00</span></h4>
        </div>
        <button class="checkout-btn" id="checkoutBtn">Proceed to Payment</button>
    </div>

    <div class="container">
        <!-- Left big box -->
        <div class="box big box1">
            <h3>NEW ARRIVALS</h3>
            <h2>Discover Real Flavors</h2>
            <a href="#" class="btn orange shop-btn" data-category="Fruit">Shop now</a>
        </div>

        <!-- Middle two small boxes -->
        <div class="column">
            <div class="box small box2">
                <h3>NATURAL FRESH</h3>
                <h2>Herbal Shots</h2>
                <a href="#" class="btn yellow shop-btn" data-category="Herbal">Shop now</a>
            </div>
            <div class="box small box3">
                <h3>Green and Organic</h3>
                <h2>Vegetable Soups</h2>
                <a href="#" class="btn red shop-btn" data-category="Vegetable">Shop now</a>
            </div>
        </div>

        <!-- Right big box -->
        <div class="box big box4">
            <h3>Blended</h3>
            <h2>Fresh Mix Juices</h2>
            <a href="#" class="btn green shop-btn" data-category="Mix">Shop now</a>
        </div>
    </div>

    <section class="customized-products">
        <h2>Customise Juice</h2>
        <div class="custombox">
            <h3>Make Your Own Juice</h3>
            <a href="custom.php" class="makebtn">Make now</a>
        </div>
    </section>

    <section class="categories">
        <h2>Categories</h2>
        <div class="category-container">

            <div class="category-box" data-category="all">
                <img src="images/all.png" alt="alljuice">
                <p>All Juices</p>
            </div>

            <div class="category-box" data-category="Fruit">
                <img src="images/kiwi.png" alt="fruitjuice">
                <p>Fruit Juice</p>
            </div>

            <div class="category-box" data-category="Vegetable">
                <img src="images/vegetables.png" alt="vegetablejuice">
                <p>Vegetable Soups</p>
            </div>

            <div class="category-box" data-category="Mix">
                <img src="images/healthy-food.png" alt="mixfruitjuice">
                <p>Mix Fruit Juice</p>
            </div>

        </div>
    </section>



    <!-- Product Section -->
    <section class="product-section">
        <h2>Our Juices</h2>
        <div id="products-container" class="product-container">

            <?php
            $conn = new mysqli("localhost", "root", "", "dbjuice");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $category = isset($_GET['category']) ? $_GET['category'] : '';
            $q = isset($_GET['q']) ? $_GET['q'] : '';

            $sql = "SELECT * FROM products WHERE is_deleted = 0 AND category != 'Herbal'";

            // Category filter
            if ($category != '' && $category != 'all') {
                $sql .= " AND category = '" . $conn->real_escape_string($category) . "'";
            }

            // Search filter
            if ($q != '') {
                $q = $conn->real_escape_string($q);
                $sql .= " AND (name LIKE '%$q%' OR ingredients LIKE '%$q%')";
            }

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $originalPrice = (int)$row['price'];
                    $price750 = max(0, $originalPrice - 70);
                    $price500 = max(0, $originalPrice - 110);

                    echo "
<div class='product-card' id='product{$row['id']}'>
  <div class='product-card-content'>
    <a href='product.php?id={$row['id']}#product{$row['id']}'>
      <div class='flip-container'>
        <div class='flip-inner'>
          <!-- Front Side (Image) -->
          <div class='flip-front'>
            <img src='{$row['image_path']}' alt='{$row['name']}' class='product-image'>
          </div>
          <!-- Back Side (Static Info) -->
          <div class='flip-back'>
          <h4>Ingredients:</h4>
                <p>{$row['ingredients']}</p>
            <p><strong>Benefits:</strong> Boosts immunity, refreshes body</p>
            <p><strong>Storage:</strong> Keep refrigerated</p>
          </div>
        </div>
      </div>
      <h3>{$row['name']}</h3>
    </a>

    <p>{$row['description']}</p>

    <!-- Package Option Dropdown -->
    <label for='size{$row['id']}'>Choose Package:</label>
    <select class='package-select' id='size{$row['id']}'>
      <option value='500ml' data-price='{$price500}'>500ml - ₹{$price500}</option>
      <option value='750ml' data-price='{$price750}'>750ml - ₹{$price750}</option>
      <option value='1L' data-price='{$originalPrice}' selected>1L - ₹{$originalPrice}</option>
    </select>

    <p class='price'>
      <span class='product-price'>₹ {$originalPrice}</span>
    </p>

    <button class='add-to-cart' data-id='{$row['id']}'>Add to Cart</button>
  </div>
</div>";
                }
            } else {
                echo "<p>No products found.</p>";
            }

            ?>
        </div>
    </section>

    <section class="deal-section">
        <div class="deal-content">
            <p class="sub-heading">Special Products</p>
            <h2>Deals of the days.</h2>

            <!-- Countdown -->
            <div class="countdown">
                <div><span id="days">00</span><small>DAYS</small></div>
                <div><span id="hours">00</span><small>HOURS</small></div>
                <div><span id="minutes">00</span><small>MINS</small></div>
                <div><span id="seconds">00</span><small>SECS</small></div>
            </div>

            <button class="shop-btn" data-category="Mix">Shop now</button>
        </div>

        <!-- Product Image -->
        <div class="deal-image">
            <img src="images/js5.png" alt="Deal Product">
            <img src="images/js8.png" alt="Deal Product">
            <img src="images/js2.png" alt="Deal Product">
            <div class="sale-badge">30% OFF</div>
        </div>
    </section>


    <!-- ================== Shots Section ================== -->
    <section class="product-section">
        <h2>Herbal Shots</h2>
        <div id="shots-container" class="product-container">
            <?php
            $conn = new mysqli("localhost", "root", "", "dbjuice");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch only Shots products
            $sql = "SELECT * FROM products WHERE is_deleted = 0 AND category = 'Herbal'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $price100 = (int)$row['price']; // Shots usually 100ml (adjust if needed)

                    echo "
                <div class='product-card' id='product{$row['id']}'>
  <div class='product-card-content'>
    <a href='product.php?id={$row['id']}#product{$row['id']}'>
      <div class='flip-container'>
        <div class='flip-inner'>
          <!-- Front Side (Image) -->
          <div class='flip-front'>
            <img src='{$row['image_path']}' alt='{$row['name']}' class='product-image'>
          </div>
          <!-- Back Side (Static Info) -->
          <div class='flip-back'>
          <h4>Ingredients</h4>
                <p>{$row['ingredients']}</p>  
          <p><strong>Benefits:</strong> Boosts immunity, refreshes body</p>
            <p><strong>Storage:</strong> Keep refrigerated</p>
          </div>
        </div>
      </div>
      <h3>{$row['name']}</h3>
    </a>
                        <p>{$row['description']}</p>

                        <!-- Fixed size for Shots -->
                        <p class='price'>
                            <span class='product-price'>₹ {$price100}</span>
                        </p>

                        <button class='add-to-cart' data-id='{$row['id']}'>Add to Cart</button>
                    </div>
                </div>";
                }
            } else {
                echo "<p>No shots available.</p>";
            }
            ?>
        </div>
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

    <!-- Script for Animations and Cart Functionality -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="productscript.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const searchInput = document.getElementById("search");
            const resultsBox = document.getElementById("search-results");
            const container = document.getElementById("products-container");
            const categoryBoxes = document.querySelectorAll(".category-box");

            let currentCategory = ""; // track selected category

            // Typing → show dropdown
            searchInput.addEventListener("keyup", function() {
                let query = this.value.trim();
                if (query.length > 0) {
                    fetch("search.php?q=" + encodeURIComponent(query) + "&category=" + encodeURIComponent(currentCategory))
                        .then(res => res.text())
                        .then(data => {
                            resultsBox.innerHTML = data;
                            resultsBox.style.display = "block";

                            // Attach click events to each suggestion
                            document.querySelectorAll(".search-item").forEach(item => {
                                item.addEventListener("click", () => {
                                    const targetId = item.getAttribute("data-id");
                                    resultsBox.style.display = "none"; // hide dropdown

                                    const target = document.getElementById(targetId);
                                    if (target) {
                                        target.scrollIntoView({
                                            behavior: "smooth",
                                            block: "center"
                                        });
                                        target.classList.add("highlight");
                                        setTimeout(() => target.classList.remove("highlight"), 2000);
                                    } else {
                                        // reload products with category + query
                                        fetch("fetch_products.php?category=" + encodeURIComponent(currentCategory) + "&q=" + encodeURIComponent(query))
                                            .then(res => res.text())
                                            .then(html => {
                                                container.innerHTML = html;
                                                const newTarget = document.getElementById(targetId);
                                                if (newTarget) {
                                                    newTarget.scrollIntoView({
                                                        behavior: "smooth",
                                                        block: "center"
                                                    });
                                                    newTarget.classList.add("highlight");
                                                    setTimeout(() => newTarget.classList.remove("highlight"), 2000);
                                                }
                                            });
                                    }
                                });
                            });
                        });
                } else {
                    resultsBox.style.display = "none";
                }
            });

            // Category filter
            categoryBoxes.forEach(box => {
                box.addEventListener("click", () => {
                    currentCategory = box.dataset.category === "all" ? "" : box.dataset.category;
                    fetch("fetch_products.php?category=" + encodeURIComponent(currentCategory))
                        .then(res => res.text())
                        .then(html => {
                            container.innerHTML = html;
                        });
                });
            });
        });
    </script>




    <script>
        // Check if we already stored an end date
        let countdownDate = localStorage.getItem("countdownDate");

        if (!countdownDate) {
            // If not stored, set new end date (10 days from now)
            let newDate = new Date();
            newDate.setDate(newDate.getDate() + 10);
            countdownDate = newDate.getTime();
            localStorage.setItem("countdownDate", countdownDate);
        } else {
            countdownDate = parseInt(countdownDate);
        }

        function updateCountdown() {
            let now = new Date().getTime();
            let distance = countdownDate - now;

            if (distance < 0) {
                document.querySelector(".countdown").innerHTML = "<h3>Offer Expired</h3>";
                return;
            }

            let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("days").innerText = days;
            document.getElementById("hours").innerText = hours;
            document.getElementById("minutes").innerText = minutes;
            document.getElementById("seconds").innerText = seconds;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.location.hash) {
                let element = document.querySelector(window.location.hash);
                if (element) {
                    element.scrollIntoView({
                        behavior: "smooth",
                        block: "center"
                    });
                }
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const productContainer = document.getElementById("products-container");

            document.querySelectorAll(".shop-btn").forEach(btn => {
                btn.addEventListener("click", function(e) {
                    e.preventDefault();
                    const category = this.dataset.category;

                    if (category === "Herbal") {
                        // Jump directly to Herbal Shots section
                        document.querySelector("#shots-container").scrollIntoView({
                            behavior: "smooth",
                            block: "start"
                        });
                    } else {
                        // Fetch filtered products for Fruit, Vegetable, Mix
                        fetch("fetch_products.php?category=" + encodeURIComponent(category))
                            .then(res => res.text())
                            .then(html => {
                                productContainer.innerHTML = html;

                                // Scroll smoothly to products section
                                document.querySelector(".product-section").scrollIntoView({
                                    behavior: "smooth",
                                    block: "start"
                                });
                            });
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check if the URL contains a hash
            const hash = window.location.hash;
            if (hash) {
                // If a hash exists, scroll to the element with that ID
                const element = document.querySelector(hash);
                if (element) {
                    // Scroll the page to the element
                    element.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });
                }
            }
        });
    </script>


    <!-- Address Form Modal -->
    <div id="addressModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Enter Delivery Address</h2>
            <form method="POST">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="text" name="address_line" placeholder="Address Line" required>
                <input type="text" name="city" placeholder="City" required>
                <input type="text" name="state" placeholder="State" required>
                <input type="text" name="postal_code" placeholder="Postal Code" required>
                <select name="address_type">
                    <option value="Home">Home</option>
                    <option value="Office">Office</option>
                    <option value="Other">Other</option>
                </select>
                <button type="submit" name="save_address">Save Address</button>
            </form>
        </div>
    </div>

    <script>
        // Show modal when button clicked
        document.getElementById("checkoutBtn").onclick = function() {
            document.getElementById("addressModal").style.display = "flex";
        }

        // Close modal
        document.querySelector(".close").onclick = function() {
            document.getElementById("addressModal").style.display = "none";
        }

        // Close if clicked outside modal
        window.onclick = function(event) {
            if (event.target == document.getElementById("addressModal")) {
                document.getElementById("addressModal").style.display = "none";
            }
        }
    </script>


</body>

</html>