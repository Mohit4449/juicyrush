<?php
$conn = new mysqli("localhost", "root", "", "dbjuice");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['q'])){
    $q = $conn->real_escape_string($_GET['q']);
    $category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

    $sql = "SELECT * FROM products WHERE (name LIKE '%$q%' OR ingredients LIKE '%$q%')";
    if ($category != '' && $category != 'all') {
        $sql .= " AND category = '$category'";
    }
    $sql .= " LIMIT 10";

    $result = $conn->query($sql);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "
            <div class='search-item' data-id='product{$row['id']}'>
                <img src='".$row['image_path']."' alt='".$row['name']."'>
                <div>
                    <strong>".$row['name']."</strong><br>
                    ₹".$row['price']."
                </div>
            </div>";
        }
    } else {
        echo "<div class='search-item'>No results found</div>";
    }
}
?>
