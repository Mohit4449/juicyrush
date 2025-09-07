<?php
// user_make_custom_juice.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['username'])) {
    // send user to login and tell login where to come back
    $current = $_SERVER['REQUEST_URI']; // includes querystring if any
    header("Location: login.php?redirect=" . urlencode($current));
    exit;
}


// Fetch options
function fetchAll($conn, $type)
{
    $stmt = $conn->prepare("SELECT * FROM custom_juice WHERE type=? AND is_active=1 ORDER BY name");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$sizes = fetchAll($conn, 'size');
$fruits = fetchAll($conn, 'fruit');
$ingredients = fetchAll($conn, 'ingredient');
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <title>Make Your Own Juice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .img-500 {
            width: 500px;
            height: 500px;
            object-fit: cover;
            border-radius: 16px;
        }
    </style>
</head>

<body class="bg-gradient-to-b from-orange-50 to-white text-gray-900">
    <div class="max-w-7xl mx-auto p-6">
        <header class="flex items-center justify-between mb-8">
            <h1 class="text-3xl md:text-4xl font-extrabold">Make Your Own Juice</h1>
            <a href="product.php" class="px-4 py-2 rounded-xl bg-black text-white">Back to Home</a>
        </header>

        <!-- Sizes -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold mb-4">Choose Bottle Size</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <?php foreach ($sizes as $s): ?>
                    <label class="block bg-white rounded-2xl shadow p-5 cursor-pointer border hover:border-black">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="size" class="size-input scale-125"
                                value="<?php echo $s['id']; ?>"
                                data-volume="<?php echo $s['volume_ml']; ?>"
                                data-price="<?php echo $s['price']; ?>">
                            <div>
                                <div class="text-lg font-semibold"><?php echo htmlspecialchars($s['name']); ?></div>
                                <div class="text-sm text-gray-600">
                                    Volume: <?php echo intval($s['volume_ml']); ?> ml • Base: ₹<?php echo number_format($s['price'], 2); ?>
                                </div>
                            </div>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Fruits -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold mb-4">Pick Fruits</h2>
            <p class="text-sm text-gray-600 mb-3">Fruit price is per 100ml of final bottle volume.</p>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($fruits as $f): ?>
                    <label class="block bg-white rounded-2xl shadow p-4 border hover:border-black">
                        <?php if ($f['image']): ?>
                            <img src="<?php echo htmlspecialchars($f['image']); ?>" class="w-full aspect-square object-cover rounded-xl mb-3" alt="">
                        <?php else: ?>
                            <div class="w-full aspect-square bg-gray-100 rounded-xl grid place-items-center mb-3">No Image</div>
                        <?php endif; ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold"><?php echo htmlspecialchars($f['name']); ?></div>
                                <div class="text-xs text-gray-600">₹<?php echo number_format($f['price'], 2); ?> / 100ml</div>
                            </div>
                            <input type="checkbox"
                                class="fruit-input w-5 h-5"
                                value="<?php echo $f['id']; ?>"
                                data-name="<?php echo htmlspecialchars($f['name']); ?>"
                                data-per100="<?php echo $f['price']; ?>">
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Ingredients -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold mb-4">Add Ingredients (Optional)</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <?php foreach ($ingredients as $ing): ?>
                    <label class="block bg-white rounded-2xl shadow p-4 border hover:border-black">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold"><?php echo htmlspecialchars($ing['name']); ?></div>
                                <div class="text-xs text-gray-600">+ ₹<?php echo number_format($ing['price'], 2); ?></div>
                            </div>
                            <input type="checkbox"
                                class="ing-input w-5 h-5"
                                value="<?php echo $ing['id']; ?>"
                                data-name="<?php echo htmlspecialchars($ing['name']); ?>"
                                data-price="<?php echo $ing['price']; ?>">
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Summary -->
        <section class="bg-white rounded-2xl shadow p-6 sticky bottom-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="text-lg font-semibold">Summary</div>
                    <div class="text-sm text-gray-700" id="summaryText">Pick a size and fruits to see price.</div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Total</div>
                    <div class="text-3xl font-extrabold">₹<span id="grandTotal">0.00</span></div>
                </div>
                <form id="customJuiceForm" method="post" action="process_custom_order.php">
                    <input type="hidden" name="size_id" id="size_id">
                    <input type="hidden" name="fruits" id="fruits_json">
                    <input type="hidden" name="ingredients" id="ings_json">
                    <input type="hidden" name="total_volume_ml" id="total_volume_ml">
                    <input type="hidden" name="total_amount" id="total_amount">
                    <button type="submit" class="px-5 py-3 rounded-xl bg-black text-white text-lg">Order Now</button>
                </form>
            </div>
        </section>
    </div>

    <script>
        function money(n) {
            return (Math.round(n * 100) / 100).toFixed(2);
        }

        const sizeInputs = document.querySelectorAll('.size-input');
        const fruitInputs = document.querySelectorAll('.fruit-input');
        const ingInputs = document.querySelectorAll('.ing-input');

        let chosenSize = null;

        function recalc() {
            let base = 0;
            let vol = 0;
            if (chosenSize) {
                base = parseFloat(chosenSize.dataset.price || '0');
                vol = parseInt(chosenSize.dataset.volume || '0', 10);
            }

            // fruits: per-100ml price × (vol/100)
            const selectedFruits = [];
            let fruitSum = 0;
            fruitInputs.forEach(cb => {
                if (cb.checked) {
                    const per100 = parseFloat(cb.dataset.per100 || '0');
                    fruitSum += per100 * (vol / 100);
                    selectedFruits.push({
                        id: parseInt(cb.value, 10),
                        name: cb.dataset.name,
                        per100ml: per100
                    });
                }
            });

            // ingredients: flat add-on
            const selectedIngs = [];
            let ingSum = 0;
            ingInputs.forEach(cb => {
                if (cb.checked) {
                    const p = parseFloat(cb.dataset.price || '0');
                    ingSum += p;
                    selectedIngs.push({
                        id: parseInt(cb.value, 10),
                        name: cb.dataset.name,
                        price: p
                    });
                }
            });

            const total = base + fruitSum + ingSum;

            // UI summary
            const parts = [];
            if (chosenSize) parts.push(`${chosenSize.closest('label').querySelector('.text-lg')?.textContent || 'Size'} (₹${money(base)})`);
            if (selectedFruits.length) parts.push(`${selectedFruits.length} fruit(s) (₹${money(fruitSum)})`);
            if (selectedIngs.length) parts.push(`${selectedIngs.length} ingredient(s) (₹${money(ingSum)})`);

            document.getElementById('summaryText').textContent = parts.length ? parts.join(' • ') : 'Pick a size and fruits to see price.';
            document.getElementById('grandTotal').textContent = money(total);

            // Fill form hidden fields
            document.getElementById('size_id').value = chosenSize ? parseInt(chosenSize.value, 10) : '';
            document.getElementById('fruits_json').value = JSON.stringify(selectedFruits);
            document.getElementById('ings_json').value = JSON.stringify(selectedIngs);
            document.getElementById('total_volume_ml').value = vol || '';
            document.getElementById('total_amount').value = total ? money(total) : '';
        }

        sizeInputs.forEach(r => {
            r.addEventListener('change', () => {
                chosenSize = r;
                recalc();
            });
        });
        fruitInputs.forEach(cb => cb.addEventListener('change', recalc));
        ingInputs.forEach(cb => cb.addEventListener('change', recalc));
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('customJuiceForm').addEventListener('submit', function(e) {
            e.preventDefault(); // stop normal form submit

            const formData = new FormData(this);

            fetch('process_custom_order.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Placed!',
                            text: 'Your custom juice order has been successfully placed.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'product.php'; // redirect after OK
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Order Failed',
                            text: data.error || 'Something went wrong. Please try again.'
                        });
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to place order. Please try again.'
                    });
                    console.error(err);
                });
        });
    </script>

</body>

</html>