<?php
$conn = new mysqli('localhost', 'root', '', 'dbjuice');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Create products table with image field
$tableQuery = "CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT NOT NULL,
    ingredients TEXT NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL
)";
$conn->query($tableQuery);

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $image = $_FILES['image']['name'];
    $target = 'uploads/' . basename($image);

    $sql = "INSERT INTO products (name, price, description, ingredients, image_path) 
            VALUES ('$name', '$price', '$description', '$ingredients', '$target')";
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target) && $conn->query($sql)) {
        header('Location: adproduct.php');
        exit();
    } else {
        echo 'Failed to upload image.';
    }
}

if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $image = $_FILES['image']['name'];
    $target = 'uploads/' . basename($image);

    $sql = "UPDATE products SET name='$name', price='$price', description='$description', ingredients='$ingredients' WHERE id='$id'";
    $conn->query($sql);

    if (!empty($image)) {
        $imageSql = "UPDATE products SET image_path='$target' WHERE id='$id'";
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $conn->query($imageSql);
    }
    header('Location: adproduct.php');
    exit();
}

if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product'];

    $sql = "DELETE FROM products WHERE id='$id'";
    $conn->query($sql);
    header('Location: adproduct.php');
    exit();
}

$result = $conn->query("SELECT * FROM products");
$conn->close();
?>
