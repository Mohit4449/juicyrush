<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <title>Login</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="style/logstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        .input-box {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="logo">
            <a href="home.php"><img src="images/logo-removebg-preview.png" alt="Juice Logo"></a>
        </div>
    </header>

    <div class="container">
        <!-- Login Form -->
        <div class="form-box login">
            <form action="auth.php" method="post">
                <h1>Login</h1>

                <?php
                if (isset($_SESSION['error'])) {
                    echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
                    unset($_SESSION['error']);
                }
                ?>

                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
                    <!-- Eye toggle -->
                    <i class='bx bx-show toggle-password' onclick="togglePassword('loginPassword', this)"></i>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
        </div>

        <!-- Register Form -->
        <div class="form-box register">
            <form action="auth.php" method="post">
                <h1>Registration</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="registerPassword" placeholder="Password" required>
                    <!-- Eye toggle -->
                    <i class='bx bx-show toggle-password' onclick="togglePassword('registerPassword', this)"></i>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
            </form>
        </div>

        <!-- Toggle Panels -->
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Show/Hide Password Toggle
        function togglePassword(fieldId, icon) {
            const passwordField = document.getElementById(fieldId);
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("bx-show");
                icon.classList.add("bx-hide");
            } else {
                passwordField.type = "password";
                icon.classList.remove("bx-hide");
                icon.classList.add("bx-show");
            }
        }

        // Validation
        document.querySelector('.form-box.register form').addEventListener('submit', function(e) {
            const username = this.username.value.trim();
            const email = this.email.value.trim();
            const password = this.password.value;

            if (username.length < 3) {
                alert("Username must be at least 3 characters.");
                e.preventDefault();
            } else if (!/^[^@]+@[^@]+\.[a-z]{2,}$/i.test(email)) {
                alert("Invalid email format.");
                e.preventDefault();
            } else if (password.length < 6) {
                alert("Password must be at least 6 characters.");
                e.preventDefault();
            }
        });

        document.querySelector('.form-box.login form').addEventListener('submit', function(e) {
            const username = this.username.value.trim();
            const password = this.password.value;

            if (!username || !password) {
                alert("Both username and password are required.");
                e.preventDefault();
            }
        });
    </script>

</body>

</html>