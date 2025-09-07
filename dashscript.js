// ===================== SIDEBAR =====================
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.querySelector(".main-content");
    if (sidebar && mainContent) {
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("expanded");
    }
}

// ===================== PRODUCT FORM =====================
function fillForm(id, name, price, description) {
    document.getElementById('id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('price').value = price;
    document.getElementById('description').value = description;
}

function editProduct(id, name, price, description, ingredients) {
    document.getElementById('id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('price').value = price;
    document.getElementById('description').value = description;
    document.getElementById('ingredients').value = ingredients;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function deleteProduct(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ff5733",
        cancelButtonColor: "#555",
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "actions.php?delete_product=" + id;
        }
    });
}

// ===================== NOTIFICATIONS =====================
function fetchNotifications() {
    fetch("notifications.php")
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                const badge = document.getElementById("badgeCount");
                if (badge) {
                    badge.innerText = data.length;
                    badge.style.display = "inline";
                }

                data.forEach(n => showToast(`🆕 Order #${n.id} from User ${n.user_id}`));

                let audio = new Audio("notification.mp3");
                audio.play();
            }
        })
        .catch(err => console.error("Notification error:", err));
}

function showToast(message) {
    const toast = document.createElement("div");
    toast.className = "toast";
    toast.innerText = message;
    document.getElementById("toastContainer")?.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

setInterval(fetchNotifications, 5000);

// ===================== ORDERS DROPDOWN =====================
document.addEventListener("DOMContentLoaded", function () {
    const bell = document.getElementById("notificationBell");
    const dropdown = document.getElementById("notificationDropdown");
    const orderList = document.getElementById("orderList");
    const badge = document.getElementById("badgeCount");

    if (bell && dropdown && orderList) {
        bell.addEventListener("click", function () {
            dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
            if (badge) badge.style.display = "none";
        });

        function fetchOrders() {
            fetch("fetch_orders.php")
                .then(res => res.json())
                .then(data => {
                    orderList.innerHTML = "";
                    if (data.length > 0) {
                        data.forEach(order => {
                            const li = document.createElement("li");
                            li.classList.add("order-item");

                            let detailsHTML = "";
                            try {
                                const items = JSON.parse(order.order_details);
                                detailsHTML = "<ul>";
                                items.forEach(item => {
                                    detailsHTML += `<li>${item.name} - ₹${item.price}</li>`;
                                });
                                detailsHTML += "</ul>";
                            } catch {
                                detailsHTML = order.order_details;
                            }

                            li.innerHTML = `
                                <strong>Order #${order.id}</strong><br>
                                User: ${order.username}<br>
                                Items: ${order.total_items} | Amount: ₹${order.total_amount}<br>
                                <strong>Details:</strong><br>
                                ${detailsHTML}
                                <small>${order.date_of_order}</small>
                            `;
                            orderList.appendChild(li);
                        });
                        if (badge) {
                            badge.innerText = data.length;
                            badge.style.display = "inline-block";
                        }
                    }
                })
                .catch(err => console.error("Error fetching orders:", err));
        }

        setInterval(fetchOrders, 5000);
        fetchOrders();
    }
});

// ===================== THEME SWITCH =====================

// Helper: update charts safely
function updateChartColors(isDark) {
    const textColor = isDark ? "#f9fafb" : "#1f2937";
    const gridColor = isDark ? "#2D2D30" : "#e5e7eb";

    if (typeof revenueChart !== "undefined") {
        revenueChart.options.plugins.legend.labels.color = textColor;
        revenueChart.options.scales.x.ticks.color = textColor;
        revenueChart.options.scales.x.grid.color = gridColor;
        revenueChart.options.scales.y.ticks.color = textColor;
        revenueChart.options.scales.y.grid.color = gridColor;
        revenueChart.update();
    }

    if (typeof productChart !== "undefined") {
        productChart.options.plugins.legend.labels.color = textColor;
        productChart.options.scales.x.ticks.color = textColor;
        productChart.options.scales.x.grid.color = gridColor;
        productChart.options.scales.y.ticks.color = textColor;
        productChart.options.scales.y.grid.color = gridColor;
        productChart.update();
    }
}

// Apply saved theme instantly (before DOM ready)
(function applyInitialTheme() {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");
    } else {
        document.body.classList.remove("dark-mode");
    }
})();

// Setup toggle after DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    const themeSwitch = document.getElementById("themeSwitch");
    if (!themeSwitch) return;

    const isDark = document.body.classList.contains("dark-mode");
    themeSwitch.checked = isDark;
    updateChartColors(isDark);
    if (typeof updateLogo === "function") updateLogo(isDark);

    themeSwitch.addEventListener("change", () => {
        const nowDark = themeSwitch.checked;
        if (nowDark) {
            document.body.classList.add("dark-mode");
            localStorage.setItem("theme", "dark");
        } else {
            document.body.classList.remove("dark-mode");
            localStorage.setItem("theme", "light");
        }
        updateChartColors(nowDark);
        if (typeof updateLogo === "function") updateLogo(nowDark);
    });
});
