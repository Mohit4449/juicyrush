<?php
include 'config.php'; // Make sure to replace with your actual database connection file

header('Content-Type: application/json');

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['username'], $data['orderDetails'], $data['totalItems'], $data['totalAmount'])) {
    $username = $data['username'];
    $orderDetails = $data['orderDetails'];
    $totalItems = intval($data['totalItems']);
    $totalAmount = floatval($data['totalAmount']);

    // Get the user ID from the username
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];

        // Insert the order into the database
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
?>
