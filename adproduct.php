<!DOCTYPE html>
<html>

<head>
    <title>Product Management</title>
    <link rel="stylesheet" href="adproductstyle.css">
    <script src="dashscript.js" defer></script>
</head>

<body>
    <div class="sidebar" id="sidebar">
        <div class="logo-container">
            <img src="images/logo-removebg-preview.png" alt="Juice Logo" class="logo">
        </div>
        <nav class="nav-links">
            <a href="dashboard.php" class="nav-link">Home</a>
            <a href="adproduct.php" class="nav-link">Products</a>
            <a href="order.php" class="nav-link">Orders</a>
            <a href="customer.php" class="nav-link">Customer</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </nav>
    </div>

    <div class="main-content" id="mainContent">
        <button class="openbtn" onclick="toggleSidebar()">☰ Menu</button>
        <h2>Product Management</h2>

        <form method="post" action="actions.php" enctype="multipart/form-data">
            <input type="hidden" id="id" name="id">
            <input type="text" id="name" name="name" placeholder="Product Name" required>
            <input type="number" id="price" name="price" placeholder="Price" required>
            <textarea id="description" name="description" placeholder="Description" required></textarea><br>
            <textarea id="ingredients" name="ingredients" placeholder="Ingredients" required></textarea><br>
            <input type="file" id="image" name="image" accept="image/*" required><br>
            <button type="submit" name="add_product">Add Product</button>
            <button type="submit" name="update_product">Update Product</button>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Ingredients</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
            <?php
            include 'actions.php';
            while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><?php echo $row['ingredients']; ?></td>
                    <td>
                        <?php if (!empty($row['image_path'])) { ?>
                            <img src="<?php echo $row['image_path']; ?>" alt="Product Image" style="width:50px;height:50px;">
                        <?php } ?>
                    </td>
                    <td>
                        <button type="button"
                            onclick="document.getElementById('id').value = '<?php echo $row['id']; ?>';
                 document.getElementById('name').value = '<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>';
                 document.getElementById('price').value = '<?php echo $row['price']; ?>';
                 document.getElementById('description').value = '<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>';
                 document.getElementById('ingredients').value = '<?php echo htmlspecialchars($row['ingredients'], ENT_QUOTES); ?>';">
                            Edit
                        </button>

                    <form method="post" action="actions.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete_product" class="delete">Delete</button>
                    </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>

</html>