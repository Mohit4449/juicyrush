<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbjuice";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if (isset($_POST['send'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Insert data into the database
    $sql = "INSERT INTO contact (name, phone, email, message) VALUES ('$name', '$phone', '$email', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Message sent successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Page</title>
    <link rel="stylesheet" href="style/contactstyle.css">
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
        </div>
        </div>
    </header>
    <section class="bgstrap">
        <div class="strap">
            <img src="images/strap.png" alt="strap">
        </div>
    </section>
    <section class="contact-info">
        <div class="info-box">
            <h2>HEAD OFFICE</h2>
            <p>Email: Juicy_rush01@Mail.Com</p>
            <p>Contact us at: 6566676869</p>
        </div>
        <div class="info-box">
            <h2>PRODUCTION FACILITY</h2>
            <p>Email: Juicy_rush02@Mail.Com</p>
            <p>Contact us at: 6566676870</p>
        </div>
    </section>

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
</body>

</html>