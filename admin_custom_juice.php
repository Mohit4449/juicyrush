<?php
$current_page = basename($_SERVER['PHP_SELF']);
session_start();
require_once 'config.php';

$errors = [];
$success = null;

function sanitize($s)
{
    return trim($s ?? '');
}
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

if (is_post()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'create' || $action === 'update') {
        $id       = intval($_POST['id'] ?? 0);
        $type     = $_POST['type'] ?? '';
        $name     = sanitize($_POST['name'] ?? '');
        $price    = floatval($_POST['price'] ?? 0);
        $volume   = $_POST['volume_ml'] !== '' ? intval($_POST['volume_ml']) : null;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (!in_array($type, ['fruit', 'size', 'ingredient'], true)) $errors[] = "Invalid type.";
        if ($name === '') $errors[] = "Name is required.";
        if ($type === 'size' && !$volume) $errors[] = "Volume (ml) required for size.";
        if ($type !== 'size') $volume = null;

        $imagePath = null;
        if ($type === 'fruit' && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $dir = __DIR__ . '/uploads/fruits/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fname = 'fruit_' . time() . '_' . mt_rand(1000, 9999) . '.' . strtolower($ext);
            $dest = $dir . $fname;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $imagePath = 'uploads/fruits/' . $fname;
            } else $errors[] = "Failed to upload fruit image.";
        }

        if (empty($errors)) {
            if ($action === 'create') {
                $sql = "INSERT INTO custom_juice (type,name,image,price,volume_ml,is_active) VALUES (?,?,?,?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssdis", $type, $name, $imagePath, $price, $volume, $isActive);
                $ok = $stmt->execute();
                $ok ? $success = "Created successfully." : $errors[] = $stmt->error;
            } else {
                if ($imagePath) {
                    $sql = "UPDATE custom_juice SET type=?,name=?,image=?,price=?,volume_ml=?,is_active=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssdisi", $type, $name, $imagePath, $price, $volume, $isActive, $id);
                } else {
                    $sql = "UPDATE custom_juice SET type=?,name=?,price=?,volume_ml=?,is_active=? WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssdisi", $type, $name, $price, $volume, $isActive, $id);
                }
                $ok = $stmt->execute();
                $ok ? $success = "Updated successfully." : $errors[] = $stmt->error;
            }
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM custom_juice WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute() ? $success = "Deleted." : $errors[] = $stmt->error;
        }
    }
}

$res = $conn->query("SELECT * FROM custom_juice ORDER BY type,name");
$items = [];
while ($row = $res->fetch_assoc()) $items[] = $row;
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Admin • Custom Juice Catalog</title>
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

                <h2>Admin • Custom Juice Catalog</h2>
                <?php if ($errors): ?>
                    <div class="alert error"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
                <?php elseif ($success): ?>
                    <div class="alert success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <div class="grid-2">
                    <!-- Form -->
                    <form method="post" enctype="multipart/form-data" class="card">
                        <h3>Add / Update Item</h3>
                        <input type="hidden" name="id" id="f_id">

                        <label>Type</label>
                        <select name="type" id="f_type">
                            <option value="fruit">Fruit</option>
                            <option value="size">Size</option>
                            <option value="ingredient">Ingredient</option>
                        </select>

                        <label>Name</label>
                        <input name="name" id="f_name" required>

                        <label>Price (₹)</label>
                        <input name="price" id="f_price" type="number" step="0.01" required>
                        <small>Fruit: per 100ml • Size: base price • Ingredient: flat add-on</small>

                        <label>Volume (ml, only for Size)</label>
                        <input name="volume_ml" id="f_volume" type="number" placeholder="e.g., 500">

                        <label>Fruit Image (500×500)</label>
                        <input name="image" id="f_image" type="file" accept="image/*">

                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" id="f_active" checked>
                            Active
                        </label>
                        <div class="btn-group">
                            <button name="action" value="create" class="btn black">Create</button>
                            <button name="action" value="update" class="btn blue">Update</button>
                        </div>
                    </form>

                    <!-- Tips -->
                    <div class="card">
                        <h3>Tips</h3>
                        <ul>
                            <li>• Keep fruit images square (500×500).</li>
                            <li>• Sizes need a volume in ml and base price.</li>
                            <li>• Ingredients are optional add-ons.</li>
                        </ul>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-card">
                    <h3>Catalog Items</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Volume</th>
                                <th>Image</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><?= $it['id'] ?></td>
                                    <td><?= htmlspecialchars($it['type']) ?></td>
                                    <td><?= htmlspecialchars($it['name']) ?></td>
                                    <td>₹<?= number_format($it['price'], 2) ?></td>
                                    <td><?= $it['volume_ml'] ? intval($it['volume_ml']) . ' ml' : '-' ?></td>
                                    <td>
                                        <?php if ($it['image']): ?>
                                            <img src="<?= htmlspecialchars($it['image']) ?>" class="thumb">
                                        <?php else: ?><span>—</span><?php endif; ?>
                                    </td>
                                    <td><?= $it['is_active'] ? 'Yes' : 'No' ?></td>
                                    <td>
                                        <button type="button" class="btn grey small edit-btn"
                                            data-item='<?= htmlspecialchars(json_encode($it, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ?>'>
                                            Edit
                                        </button>
                                        <form method="post" class="inline" onsubmit="return confirm('Delete this item?')">
                                            <input type="hidden" name="id" value="<?= $it['id'] ?>">
                                            <button name="action" value="delete" class="btn red small">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const fillForm = (it) => {
                    document.getElementById('f_id').value = it.id;
                    document.getElementById('f_type').value = it.type;
                    document.getElementById('f_name').value = it.name;
                    document.getElementById('f_price').value = it.price;
                    document.getElementById('f_volume').value = it.volume_ml ?? '';
                    document.getElementById('f_active').checked = (it.is_active == 1);
                };
                window.fillForm = fillForm;

                document.querySelectorAll('.edit-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const it = JSON.parse(btn.dataset.item);
                        fillForm(it);
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    });
                });
            });

            function toggleSidebar() {
                document.getElementById("sidebar").classList.toggle("collapsed");
            }
        </script>
</body>

</html>