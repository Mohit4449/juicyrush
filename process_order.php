<?php
session_start();
include 'config.php';

header('Content-Type: application/json');


if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit();
}

$username = $_SESSION['username'];

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['orderDetails'], $data['totalItems'], $data['totalAmount'])) {
    $orderDetails = $data['orderDetails'];
    $totalItems = intval($data['totalItems']);
    $totalAmount = floatval($data['totalAmount']);

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
        $insertStmt->bind_param("isid", $userId, $orderDetails, $totalItems, $totalAmount);

        if ($insertStmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to insert order']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
}

$conn->close();
