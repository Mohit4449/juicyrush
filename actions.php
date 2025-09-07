<?php
// actions.php
// Place in same folder as adproduct.php; make sure db.php exists and sets $conn (mysqli).
include_once 'config.php';

// Ensure products table exists with is_deleted column
$conn->query("
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  description TEXT NOT NULL,
  ingredients TEXT NOT NULL,
  category VARCHAR(100) DEFAULT '',
  image_path VARCHAR(255) DEFAULT NULL,
  is_deleted TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Helper: add column if missing (safe)
function ensure_column_exists($conn, $column, $ddl)
{
    $col = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM products LIKE '$col'");
    if ($res && $res->num_rows === 0) {
        $conn->query("ALTER TABLE products ADD COLUMN $ddl");
    }
}
ensure_column_exists($conn, 'is_deleted', "is_deleted TINYINT(1) DEFAULT 0");

// ----------------- Handle form submissions (Add / Update) -----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    // Add product via form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $category = $_POST['category'] ?? '';
    $image = $_FILES['image']['name'] ?? '';
    $target = '';
    if (!empty($image)) $target = 'uploads/' . basename($image);

    $stmt = $conn->prepare("INSERT INTO products (name, price, description, ingredients, category, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssss", $name, $price, $description, $ingredients, $category, $target);
    if ((!empty($image) && move_uploaded_file($_FILES['image']['tmp_name'], $target) && $stmt->execute()) || (empty($image) && $stmt->execute())) {
        header('Location: adproduct.php');
        exit;
    } else {
        echo "Failed to add product.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    // Update product via form
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $category = $_POST['category'] ?? '';

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, ingredients=?, category=? WHERE id=?");
    $stmt->bind_param("sdsssi", $name, $price, $description, $ingredients, $category, $id);
    $stmt->execute();

    // Optional image update
    if (!empty($_FILES['image']['name'])) {
        $target = 'uploads/' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $stmt2 = $conn->prepare("UPDATE products SET image_path=? WHERE id=?");
            $stmt2->bind_param("si", $target, $id);
            $stmt2->execute();
        }
    }

    header('Location: adproduct.php');
    exit;
}

// ----------------- Handle AJAX actions -----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'];
    $id = intval($_POST['id'] ?? 0);

    if ($action === 'delete') {
        // soft delete
        $stmt = $conn->prepare("UPDATE products SET is_deleted = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        echo json_encode(['status' => $ok ? 'deleted' : 'error']);
        exit;
    }

    if ($action === 'undo') {
        $stmt = $conn->prepare("UPDATE products SET is_deleted = 0 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        echo json_encode(['status' => $ok ? 'restored' : 'error']);
        exit;
    }

    if ($action === 'permanent_delete') {
        // Only delete rows marked deleted
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND is_deleted = 1");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        echo json_encode(['status' => $ok ? 'permanently_deleted' : 'error']);
        exit;
    }

    echo json_encode(['status' => 'unknown_action']);
    exit;
}

// ----------------- GET fallback (old links) -----------------
if (isset($_GET['delete_product'])) {
    // Soft delete + redirect back to anchor so page stays in position
    $id = intval($_GET['delete_product']);
    $stmt = $conn->prepare("UPDATE products SET is_deleted = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    // redirect back to admin page with anchor so browser stays near the product area
    header("Location: adproduct.php?deleted=$id#productRow$id");
    exit;
}

if (isset($_GET['undo_delete'])) {
    $id = intval($_GET['undo_delete']);
    $stmt = $conn->prepare("UPDATE products SET is_deleted = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: adproduct.php?restored=1#productRow$id");
    exit;
}
