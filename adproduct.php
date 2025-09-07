<?php
$current_page = basename($_SERVER['PHP_SELF']);
session_start();
require_once 'config.php';

// Fetch active products
$result = $conn->query("SELECT * FROM products WHERE is_deleted = 0 ORDER BY id DESC");
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin • Normal Juice Catalog</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="style/newstyle.css">
    <script src="dashscript.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flaticon/flaticon-uicons/css/all/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="logo-container">
                <img src="images/logo.png" alt="logo">
            </div>
            <nav class="nav-links">
                <a href="dashboard.php" class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">Home</a>
                <a href="adproduct.php" class="nav-link <?= ($current_page == 'adproduct.php') ? 'active' : '' ?>">Products</a>
                <a href="admin_custom_juice.php" class="nav-link <?= ($current_page == 'admin_custom_juice.php') ? 'active' : '' ?>">Custom Products</a>
                <a href="order.php" class="nav-link <?= ($current_page == 'order.php') ? 'active' : '' ?>">Orders</a>
                <a href="customer.php" class="nav-link <?= ($current_page == 'customer.php') ? 'active' : '' ?>">Users</a>
                <a href="logout.php" class="nav-link logout">Logout</a>
                <div class="theme-toggle">
                    <input type="checkbox" id="themeSwitch" />
                    <label for="themeSwitch" class="toggle">
                        <span class="toggle-icons">
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-moon"></i>
                        </span>
                    </label>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <button class="menu-button" onclick="toggleSidebar()">☰ Menu</button>

            <h2>Admin • Normal Juice Catalog</h2>

            <div class="grid-2">
                <!-- Form -->
                <form method="post" action="actions.php" enctype="multipart/form-data" class="card">
                    <h3>Add / Update Product</h3>
                    <input type="hidden" id="id" name="id">

                    <label>Product Name</label>
                    <input type="text" id="name" name="name" required>

                    <label>Price (₹)</label>
                    <input type="number" id="price" name="price" required>

                    <label>Description</label>
                    <input type="text" id="description" name="description" required>

                    <label>Ingredients</label>
                    <input type="text" id="ingredients" name="ingredients" required>

                    <label>Category</label>
                    <select id="category" name="category" required>
                        <option value="">-- Select Category --</option>
                        <option value="Fruit">Fruit</option>
                        <option value="Herbal">Herbal</option>
                        <option value="Vegetable">Vegetable</option>
                        <option value="Mix">Mix</option>
                    </select>

                    <label>Product Image</label>
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                    <img id="preview" src="#" alt="Image Preview" class="thumb" style="display:none;">

                    <div class="btn-group">
                        <button type="submit" name="add_product" class="btn black">Add Product</button>
                        <button type="submit" name="update_product" class="btn blue">Update Product</button>
                    </div>
                </form>

                <!-- Tips -->
                <div class="card">
                    <h3>Tips</h3>
                    <ul>
                        <li>• Use high-quality product images for best display.</li>
                        <li>• Keep description short but informative.</li>
                        <li>• Ingredients help customers understand contents.</li>
                    </ul>
                </div>
            </div>

            <!-- Products Table -->
            <div class="table-card">
                <h3>Product List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Ingredients</th>
                            <th>Category</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr id="productRow<?= $row['id'] ?>">
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td>₹<?= htmlspecialchars($row['price']) ?></td>
                                <td><?= htmlspecialchars($row['description']) ?></td>
                                <td><?= htmlspecialchars($row['ingredients']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td>
                                    <?php if (!empty($row['image_path'])): ?>
                                        <img src="<?= htmlspecialchars($row['image_path']) ?>" class="thumb">
                                    <?php else: ?><span>—</span><?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn grey small edit-btn"
                                        data-id="<?= $row['id'] ?>"
                                        data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                                        data-price="<?= $row['price'] ?>"
                                        data-description="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>"
                                        data-ingredients="<?= htmlspecialchars($row['ingredients'], ENT_QUOTES) ?>"
                                        data-category="<?= htmlspecialchars($row['category'], ENT_QUOTES) ?>">
                                        Edit
                                    </button>

                                    <!-- Delete button: data-id used by JS -->
                                    <button type="button" value="delete" class="btn red small delete-btn" data-id="<?= $row['id'] ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var preview = document.getElementById('preview');
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // Autofill edit form
        document.querySelectorAll(".edit-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("id").value = this.dataset.id;
                document.getElementById("name").value = this.dataset.name;
                document.getElementById("price").value = this.dataset.price;
                document.getElementById("description").value = this.dataset.description;
                document.getElementById("ingredients").value = this.dataset.ingredients;
                document.getElementById("category").value = this.dataset.category;
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });

        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("collapsed");
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tbody = document.querySelector('table tbody');
            const toast = document.getElementById('undoToast');
            const undoBtn = toast.querySelector('.undo-btn');

            const deletedStore = {}; // store deleted rows & timers

            tbody.addEventListener('click', async function(ev) {
                const del = ev.target.closest('.delete-btn');
                if (!del) return;

                ev.preventDefault();
                const id = del.dataset.id;
                if (!id) return;

                const row = document.getElementById('productRow' + id);
                if (!row) return;

                try {
                    // Soft delete
                    const resp = await fetch('actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=delete&id=' + encodeURIComponent(id)
                    });
                    const json = await resp.json();
                    if (json.status === 'deleted') {

                        // Save row info
                        deletedStore[id] = {
                            html: row.outerHTML,
                            nextSiblingId: row.nextElementSibling ? row.nextElementSibling.id : null
                        };

                        // Fade out and remove
                        row.classList.add('fade-out');
                        setTimeout(() => row.remove(), 200);

                        // Show toast
                        showToast(id);

                        // Start permanent delete timer (10s)
                        deletedStore[id].timer = setTimeout(async () => {
                            try {
                                const resp2 = await fetch('actions.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'action=permanent_delete&id=' + encodeURIComponent(id)
                                });
                                const json2 = await resp2.json();
                                if (json2.status === 'permanently_deleted') {
                                    delete deletedStore[id]; // cleanup
                                }
                            } catch (err) {
                                console.error('Permanent delete failed', err);
                            }

                            // Hide toast after delete
                            if (toast.dataset.productId === id) toast.classList.remove('show');

                        }, 10000); // 10 seconds

                    } else {
                        alert('Delete failed.');
                    }
                } catch (err) {
                    console.error('Delete error', err);
                    alert('Error deleting product.');
                }
            });

            function showToast(productId) {
                toast.dataset.productId = productId;
                toast.querySelector('.toast-msg').textContent = 'Product deleted.';
                toast.classList.add('show');

                // Auto-hide after 10s (matches permanent delete)
                setTimeout(() => {
                    if (toast.dataset.productId === productId) {
                        toast.classList.remove('show');
                    }
                }, 5000);
            }

            // Undo click
            undoBtn.addEventListener('click', async function() {
                const id = toast.dataset.productId;
                if (!id || !deletedStore[id]) return;

                // Cancel permanent delete timer
                clearTimeout(deletedStore[id].timer);

                try {
                    const resp = await fetch('actions.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=undo&id=' + encodeURIComponent(id)
                    });
                    const json = await resp.json();
                    if (json.status === 'restored') {
                        // Restore row
                        const template = document.createElement('template');
                        template.innerHTML = deletedStore[id].html.trim();
                        const newRow = template.content.firstChild;

                        const container = document.querySelector('table tbody');
                        if (deletedStore[id].nextSiblingId) {
                            const next = document.getElementById(deletedStore[id].nextSiblingId);
                            if (next) container.insertBefore(newRow, next);
                            else container.appendChild(newRow);
                        } else container.appendChild(newRow);

                        newRow.classList.add('fade-in');
                        setTimeout(() => newRow.classList.remove('fade-in'), 300);

                        delete deletedStore[id];
                        toast.classList.remove('show');
                    } else {
                        alert('Restore failed.');
                    }
                } catch (err) {
                    console.error('Undo error', err);
                    alert('Error restoring product.');
                }
            });
        });
    </script>

    <!-- Undo Toast -->
    <div id="undoToast" class="toast">
        <span class="toast-msg"></span>
        <button class="undo-btn">Undo</button>
    </div>

    <style>
        .toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 1000;
        }

        .toast.show {
            display: flex;
        }

        .toast .undo-btn {
            background: #ff9800;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .toast .undo-btn:hover {
            background: #e68900;
        }

        .fade-out {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .fade-in {
            opacity: 0;
            animation: fadeIn 0.3s forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }
    </style>

</body>

</html>