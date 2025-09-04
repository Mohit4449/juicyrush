<?php
session_start();
include 'config.php'; // make sure this sets $conn
header('Content-Type: application/json');

// require logged-in user (same as your existing logic)
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit();
}

$username = $_SESSION['username'];

// read incoming JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit();
}

// Normalize input: support both { orderDetails: "...", totalItems, totalAmount }
// and direct array (cart)
if (isset($data['orderDetails'], $data['totalItems'], $data['totalAmount'])) {
    $orderDetailsInput = $data['orderDetails'];

    // orderDetails might already be a JSON string (as before) or an array
    if (is_array($orderDetailsInput)) {
        $orderDetailsJson = json_encode($orderDetailsInput);
    } else {
        // if it's a JSON string, ensure it's valid JSON; if not, encode as string
        $decoded = json_decode($orderDetailsInput, true);
        if ($decoded !== null) {
            $orderDetailsJson = $orderDetailsInput; // already valid JSON string
        } else {
            // fallback: wrap as JSON string
            $orderDetailsJson = json_encode($orderDetailsInput);
        }
    }

    $totalItems = intval($data['totalItems']);
    $totalAmount = floatval($data['totalAmount']);
} elseif (is_array($data)) {
    // client sent raw cart array
    $orderDetailsJson = json_encode($data);
    $totalItems = count($data);
    $totalAmount = 0;
    foreach ($data as $itm) {
        $totalAmount += floatval($itm['price'] ?? 0);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit();
}

// find user id
$query = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userId = $user['id'];

    $insertQuery = "INSERT INTO orders (user_id, order_details, total_items, total_amount) VALUES (?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertQuery);
    $insertStmt->bind_param("isid", $userId, $orderDetailsJson, $totalItems, $totalAmount);

    if ($insertStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to insert order: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'User not found']);
}

$conn->close();
