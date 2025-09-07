<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'dbjuice');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === REGISTRATION ===
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Server-side validation
    if (strlen($username) < 3 || strlen($password) < 6) {
        $_SESSION['error'] = "Username must be at least 3 characters and password at least 6.";
        header("Location: login.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: login.php");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkSql = "SELECT * FROM users WHERE username=? OR email=?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Username or email already exists!";
        header("Location: login.php");
        exit();
    }

    $insertSql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    if ($stmt->execute()) {
        header("Location: login.php?toggle=login");
        exit();
    } else {
        $_SESSION['error'] = "Registration failed: " . $conn->error;
        header("Location: login.php");
        exit();
    }
}

// === LOGIN ===
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Both fields are required.";
        header("Location: login.php");
        exit();
    }

    // 1. Check admin
    $adminSql = "SELECT * FROM admin WHERE username=?";
    $stmt = $conn->prepare($adminSql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $adminResult = $stmt->get_result();

    if ($adminResult->num_rows > 0) {
        $admin = $adminResult->fetch_assoc();
        if ($password === $admin['password']) { // Optional: Use hash here if you hash admin passwords too
            $_SESSION['username'] = $admin['username'];
            $_SESSION['role'] = 'admin';
            header("Location: dashboard.php");
            exit();
        }
    }

    // 2. Check user
    $userSql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($userSql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $userResult = $stmt->get_result();

    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = 'user';

            // Use redirect from POST or session
            $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : (isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : 'myacc.php');

            // Clear it after use
            unset($_SESSION['redirect_url']);

            header("Location: " . $redirect);
            exit();
        }
    }

    // 3. No match
    $_SESSION['error'] = "Invalid username or password!";
    header("Location: login.php");
    exit();
}

$conn->close();
