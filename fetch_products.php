<?php
$conn = new mysqli("localhost", "root", "", "dbjuice");
if ($conn->connect_error) {
    http_response_code(500);
    echo "<p>DB connection failed.</p>";
    exit;
}

$category = isset($_GET['category']) ? trim($_GET['category']) : '';

if ($category !== '') {
    // Prepared statement for safety
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM products");
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {

        // Ensure these fields exist in your table:
        // id, name, price, description, ingredients, image_path
        // and a 'category' column for filtering

   echo "
        <div class='product-card' id='product{$row['id']}'>
            <div class='product-card-content'>
                <a href='product.php?id={$row['id']}#product{$row['id']}'>
                    <img src='{$row['image_path']}' alt='{$row['name']}' class='product-image'>
                    <h3>{$row['name']}</h3>
                </a>
                <p>{$row['description']}</p>
                <p class='price'>
                    <span class='product-price'>₹ {$row['price']}</span>
                </p>
                <button class='add-to-cart'>Add to Cart</button>
            </div>
        </div>";
                }
            } else {
                echo "<p>No products found.</p>";
            }

$conn->close();

