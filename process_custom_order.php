<?php
// process_custom_order.php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

// Require login
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'User not authenticated']);
    exit;
}
$username = $_SESSION['username'];

// Resolve user_id
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
if (!$u) {
    echo json_encode(['success' => false, 'error' => 'User not found']);
    exit;
}
$user_id = intval($u['id']);

// Read POST (form submission from user_make_custom_juice.php)
$size_id         = isset($_POST['size_id']) ? intval($_POST['size_id']) : 0;
$fruits_json     = $_POST['fruits'] ?? '[]';
$ingredients_json = $_POST['ingredients'] ?? '[]';
$total_volume_ml = isset($_POST['total_volume_ml']) ? intval($_POST['total_volume_ml']) : 0;
$total_amount    = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0.0;

// Basic validation
if ($size_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Please choose a size.']);
    exit;
}
$fruits_arr = json_decode($fruits_json, true);
$ings_arr   = json_decode($ingredients_json, true);
if (!is_array($fruits_arr) || !is_array($ings_arr)) {
    echo json_encode(['success' => false, 'error' => 'Invalid selections.']);
    exit;
}

// ✅ Check if user selected at least 1 fruit
if (count($fruits_arr) < 1) {
    echo json_encode(['success' => false, 'error' => 'Please select at least one fruit for your juice.']);
    exit;
}

// Optional: Re-validate size exists and active
$stmt = $conn->prepare("SELECT id, volume_ml, price FROM custom_juice WHERE id=? AND type='size' AND is_active=1");
$stmt->bind_param("i", $size_id);
$stmt->execute();
$size = $stmt->get_result()->fetch_assoc();
if (!$size) {
    echo json_encode(['success' => false, 'error' => 'Invalid size selected.']);
    exit;
}
// Insert order
$sql = "INSERT INTO custom_order 
        (user_id, size_id, fruits, ingredients, total_volume_ml, total_amount) 
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

$fruits_json_db = json_encode($fruits_arr, JSON_UNESCAPED_UNICODE);
$ings_json_db   = json_encode($ings_arr, JSON_UNESCAPED_UNICODE);

$stmt->bind_param(
    "iissid",
    $user_id,
    $size_id,
    $fruits_json_db,
    $ings_json_db,
    $total_volume_ml,
    $total_amount
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'orderId' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
