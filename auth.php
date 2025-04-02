<?php 
session_start();
$conn = new mysqli('localhost', 'root', '', 'dbjuice');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Registration (Unchanged)
// Registration with Duplicate Check
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the username or email already exists
    $checkSql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $result = $conn->query($checkSql);

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username or email already exists!";
        header("Location: login.php");
        exit();
    } else {
        // Proceed with registration if no duplicates found
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql)) {
            header("Location: login.php?toggle=login");
            exit();
        } else {
            $_SESSION['error'] = "Registration failed: " . $conn->error;
            header("Location: login.php");
            exit();
        }
    }
}
    
// Login with Dual Table Check
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type']; // Fetch the selected user type

    if ($user_type === 'user') {
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['role'] = 'user';
            header("Location: home.php");
            exit();
        }
    } elseif ($user_type === 'admin') {
        $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $_SESSION['username'] = $username;

            $_SESSION['role'] = 'admin';
            header("Location: dashboard.php");
            exit();
        }
    }

    // If login fails
    $_SESSION['error'] = "Invalid username or password!";
    header("Location: login.php");
    exit();
}

$conn->close();
?>