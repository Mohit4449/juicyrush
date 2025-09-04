<?php
// adproduct.php
include_once 'config.php'; // must exist & create $conn (mysqli)

// Fetch active products only (user side must also use same filter)
$result = $conn->query("SELECT * FROM products WHERE is_deleted = 0 ORDER BY id DESC");

// Read URL params for fallback GET delete redirect
$deletedIdFromUrl = isset($_GET['deleted']) ? intval($_GET['deleted']) : 0;
$restoredFlag = isset($_GET['restored']) ? true : false;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <title>Product Management</title>
    <link rel="stylesheet" href="adproductstyle.css">
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
            <a href="customer.php" class="nav-link">Users</a>
            <a href="logout.php" class="nav-link logout">Logout</a>
        </nav>
    </div>

    <div class="main-content" id="mainContent">
        <button class="openbtn" onclick="toggleSidebar()">☰ Menu</button>
        <h2>Product Management</h2>

        <form method="post" action="actions.php" enctype="multipart/form-data" id="productForm">
            <input type="hidden" id="id" name="id">
            <input type="text" id="name" name="name" placeholder="Product Name" required>
            <input type="number" id="price" name="price" placeholder="Price" required>
            <textarea id="description" name="description" placeholder="Description" required></textarea><br>
            <textarea id="ingredients" name="ingredients" placeholder="Ingredients" required></textarea><br>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="">-- Select Category --</option>
                <option value="Fruit">Fruit</option>
                <option value="Herbal">Herbal</option>
                <option value="Vegetable">Vegetable</option>
                <option value="Mix">Mix</option>
            </select><br><br>

            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
            <img id="preview" src="#" alt="Image Preview" style="display: none; width: 100px; height: 100px; margin-top: 10px; border-radius: 8px;">
            <button type="submit" name="add_product">Add Product</button>
            <button type="submit" name="update_product">Update Product</button>
        </form>

        <table>
            <tr>
                <th>ID</th><th>Name</th><th>Price</th><th>Description</th><th>Ingredients</th><th>Category</th><th>Image</th><th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr id="productRow<?= $row['id'] ?>">
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['price']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['ingredients']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="img" style="width:100px;height:80px;">
                    <?php endif; ?>
                </td>
                <td style="display:flex; gap:10px; margin-top: 12px; height:100px; align-items:center;">
                    <button type="button"
                        class="edit-btn"
                        data-id="<?= $row['id'] ?>"
                        data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                        data-price="<?= $row['price'] ?>"
                        data-description="<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>"
                        data-ingredients="<?= htmlspecialchars($row['ingredients'], ENT_QUOTES) ?>"
                        data-category="<?= htmlspecialchars($row['category'], ENT_QUOTES) ?>">
                        Edit
                    </button>

                    <button type="button" class="delete" onclick="deleteProduct(<?= $row['id'] ?>)">Delete</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Undo Toast -->
    <div id="undoToast" class="undo-toast" role="status" aria-live="polite">
        <span id="undoText">Product deleted.</span>
        <button id="undoBtn" class="undo-btn" type="button">UNDO</button>
    </div>

    <script>
    // ------- Helpers -------
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
            // keep current scroll position (optional)
            window.scrollTo({top:0, behavior:'smooth'});
        });
    });

    // ------- Delete + Undo logic (AJAX) -------
    let lastDeletedId = null;
    let undoTimer = null;

    function deleteProduct(id) {
        if (!confirm('Are you sure you want to delete this product?')) return;

        // call ajax to soft-delete
        fetch('actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'delete', id: id })
        })
        .then(r => r.json())
        .then(json => {
            if (json.status === 'deleted') {
                // hide row smoothly
                const row = document.getElementById('productRow' + id);
                if (row) {
                    row.style.transition = 'opacity .25s';
                    row.style.opacity = '0';
                    setTimeout(() => { row.style.display = 'none'; }, 250);
                }

                lastDeletedId = id;
                showUndoToast();
            } else {
                alert('Unable to delete product.');
            }
        })
        .catch(() => alert('Network error.'));
    }

    function showUndoToast() {
        const toast = document.getElementById('undoToast');
        toast.classList.add('show');

        const undoBtn = document.getElementById('undoBtn');
        undoBtn.onclick = function() {
            if (!lastDeletedId) return;
            // call ajax undo
            fetch('actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'undo', id: lastDeletedId })
            })
            .then(r => r.json())
            .then(json => {
                if (json.status === 'restored') {
                    const row = document.getElementById('productRow' + lastDeletedId);
                    if (row) {
                        row.style.display = 'table-row';
                        // small fade-in
                        row.style.opacity = '0';
                        setTimeout(()=> row.style.opacity = '1', 20);
                    } else {
                        // if row is missing (e.g. page position changed), reload
                        location.reload();
                    }
                    hideToastImmediate();
                    lastDeletedId = null;
                    clearTimeout(undoTimer);
                } else {
                    alert('Undo failed.');
                }
            })
            .catch(() => alert('Network error.'));
        };

        // after 6 seconds, hide toast and permanent delete
        undoTimer = setTimeout(() => {
            hideToastImmediate();
            if (lastDeletedId) {
                // permanent delete on server (only deletes rows already soft-deleted)
                fetch('actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'permanent_delete', id: lastDeletedId })
                }).catch(()=>{/*ignore*/});
                lastDeletedId = null;
            }
        }, 12000);
    }

    function hideToastImmediate() {
        const toast = document.getElementById('undoToast');
        toast.classList.remove('show');
    }

    // ------- Support fallback when previous links redirected with ?deleted=ID#productRowID -------
    <?php if ($deletedIdFromUrl): ?>
        // If server redirected with ?deleted=ID#productRowID -> show toast for that ID
        (function() {
            lastDeletedId = <?= $deletedIdFromUrl ?>;
            // make sure the anchor exists: browser should have scrolled to #productRowID automatically
            showUndoToast();
            // remove query from URL so reloading doesn't show it again
            if (history.replaceState) {
                const clean = window.location.pathname + window.location.hash;
                history.replaceState(null, '', clean);
            }
        })();
    <?php endif; ?>

    // Optional: show restored message
    <?php if ($restoredFlag): ?>
    setTimeout(()=>{ alert('Product restored successfully'); }, 200);
    <?php endif; ?>
    </script>
</body>
</html>
