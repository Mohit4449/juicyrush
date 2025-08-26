<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Box Layout</title>
    <link rel="stylesheet" href="style/newstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
</head>

<body>
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
                <a href="product.php" class="shop-btn">Shop Now</a>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Left big box -->
        <div class="box big box1">
            <h3>NEW ARRIVALS</h3>
            <h2>Discover Real Flavors</h2>
            <a href="#" class="btn orange">Shop now</a>

        </div>

        <!-- Middle two small boxes -->
        <div class="column">
            <div class="box small box2">

                <h3>NATURAL FRESH</h3>
                <h2>Choosing Healthy Juice</h2>
                <a href="#" class="btn yellow">Shop now</a>

            </div>
            <div class="box small box3">
                <h3>Green and Organic</h3>
                <h2>30% OFF</h2>
                <a href="#" class="btn red">Shop now</a>
            </div>
        </div>

        <!-- Right big box -->
        <div class="box big box4">
            <h3>BEST SELLER</h3>
            <h2>Fresh Healthy Juice</h2>
            <a href="#" class="btn green">Shop now</a>
        </div>
    </div>



    <section class="deal-section">
        <div class="deal-content">
            <p class="sub-heading">Special Products</p>
            <h2>Deals of the days.</h2>
            <p class="product-name">Snacks Really Raspberry</p>
            <p class="price"><span class="old-price">₹400.00</span> ₹350.00</p>

            <!-- Countdown -->
            <div class="countdown">
                <div><span id="days">00</span><small>DAYS</small></div>
                <div><span id="hours">00</span><small>HOURS</small></div>
                <div><span id="minutes">00</span><small>MINS</small></div>
                <div><span id="seconds">00</span><small>SECS</small></div>
            </div>

            <button class="shop-btn">Shop now</button>
        </div>

        <!-- Product Image -->
        <div class="deal-image">
            <img src="images/new1.png" alt="Deal Product">
            <div class="sale-badge">30% OFF</div>
        </div>
    </section>

    <section class="categories">
        <h2>Popular Categories</h2>
        <div class="category-container">

            <div class="category-box">
                <img src="images/kiwi.png" alt="fruitjuice">
                <p>Fruit Juice</p>
            </div>

            <div class="category-box">
                <img src="images/herbal.png" alt="herbaljuice">
                <p>Herbal Juice</p>
            </div>

            <div class="category-box">
                <img src="images/Vegetableetables.png" alt="Vegetableetablejuice">
                <p>Vegetableetable Juice</p>
            </div>

            <div class="category-box">
                <img src="images/healthy-food.png" alt="mixfruitjuice">
                <p>Mix Fruit Juice</p>
            </div>

        </div>
    </section>

    <script>
        // Set the end date & time (example: 10 days from now)
        let countdownDate = new Date();
        countdownDate.setDate(countdownDate.getDate() + 10); // 10 days offer

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

        // Update every second
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>


    <script>
        document.getElementById("search").addEventListener("keyup", function() {
            let query = this.value;
            if (query.length > 0) {
                fetch("search.php?q=" + query)
                    .then(res => res.text())
                    .then(data => {
                        document.getElementById("search-results").innerHTML = data;
                        document.getElementById("search-results").style.display = "block";
                    });
            } else {
                document.getElementById("search-results").style.display = "none";
            }
        });
    </script>
</body>

</html>