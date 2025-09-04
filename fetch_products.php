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
    $result = $conn->query("SELECT * FROM products WHERE category != 'Herbal'");
}

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $originalPrice = (int)$row['price'];
                $price750 = max(0, $originalPrice - 70);
                $price500 = max(0, $originalPrice - 110);
        // Ensure these fields exist in your table:
        // id, name, price, description, ingredients, image_path
        // and a 'category' column for filtering

   echo "
<div class='product-card' id='product{$row['id']}'>
  <div class='product-card-content'>
    <a href='product.php?id={$row['id']}#product{$row['id']}'>
      <div class='flip-container'>
        <div class='flip-inner'>
          <!-- Front Side (Image) -->
          <div class='flip-front'>
            <img src='{$row['image_path']}' alt='{$row['name']}' class='product-image'>
          </div>
          <!-- Back Side (Static Info) -->
          <div class='flip-back'>
          <h4>Ingredients:</h4>
                <p>{$row['ingredients']}</p>
            <p><strong>Benefits:</strong> Boosts immunity, refreshes body</p>
            <p><strong>Storage:</strong> Keep refrigerated</p>
          </div>
        </div>
      </div>
      <h3>{$row['name']}</h3>
    </a>

    <p>{$row['description']}</p>

    <!-- Package Option Dropdown -->
    <label for='size{$row['id']}'>Choose Package:</label>
    <select class='package-select' id='size{$row['id']}'>
      <option value='500ml' data-price='{$price500}'>500ml - ₹{$price500}</option>
      <option value='750ml' data-price='{$price750}'>750ml - ₹{$price750}</option>
      <option value='1L' data-price='{$originalPrice}' selected>1L - ₹{$originalPrice}</option>
    </select>

    <p class='price'>
      <span class='product-price'>₹ {$originalPrice}</span>
    </p>

    <button class='add-to-cart' data-id='{$row['id']}'>Add to Cart</button>
  </div>
</div>";          
   }
            } else {
                echo "<p>No products found.</p>";
            }

$conn->close();

