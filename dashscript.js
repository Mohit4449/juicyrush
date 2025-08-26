function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.querySelector(".main-content");

    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("expanded");
}


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


document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".edit-btn").forEach(button => {
        button.addEventListener("click", function () {
            document.getElementById("id").value = this.getAttribute("data-id");
            document.getElementById("name").value = this.getAttribute("data-name");
            document.getElementById("price").value = this.getAttribute("data-price");
            document.getElementById("description").value = this.getAttribute("data-description");
            document.getElementById("ingredients").value = this.getAttribute("data-ingredients");

            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    });
});


function deleteProduct(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ff5733", 
        cancelButtonColor: "#555", 
        confirmButtonText: "Yes, delete it!",
        customClass: {
            popup: 'custom-alert-popup', 
            title: 'custom-alert-title',
            confirmButton: 'custom-alert-confirm', 
            cancelButton: 'custom-alert-cancel' 
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "actions.php?delete_product=" + id;
        }
    });
}

function fetchNotifications() {
    fetch("notifications.php")
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                document.getElementById("badgeCount").innerText = data.length;
                document.getElementById("badgeCount").style.display = "inline";

                data.forEach(n => {
                    showToast(`🆕 Order #${n.id} from User ${n.user_id}`);
                });

                // Optional: Play sound
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
    document.getElementById("toastContainer").appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => toast.remove(), 500);
    }, 4000);
}

// Start polling every 5 seconds
setInterval(fetchNotifications, 5000);
document.addEventListener("DOMContentLoaded", function () {
    const bell = document.getElementById("notificationBell");
    const dropdown = document.getElementById("notificationDropdown");
    const orderList = document.getElementById("orderList");
    const badge = document.getElementById("badgeCount");

    // Toggle dropdown on bell click
    bell.addEventListener("click", function () {
        dropdown.style.display = dropdown.style.display === "none" ? "block" : "none";
        badge.style.display = "none"; // hide badge when clicked
    });

    // Fetch latest orders
    function fetchOrders() {
        fetch("fetch_orders.php")
            .then(res => res.json())
            .then(data => {
                orderList.innerHTML = "";
                if (data.length > 0) {
                    data.forEach(order => {
                        const li = document.createElement("li");
                        li.classList.add("order-item");

                        // Parse the JSON order details
                        let detailsHTML = "";
                        try {
                            const items = JSON.parse(order.order_details);
                            detailsHTML = "<ul>";
                            items.forEach(item => {
                                detailsHTML += `<li>${item.name} - ₹${item.price}</li>`;
                            });
                            detailsHTML += "</ul>";
                        } catch (e) {
                            detailsHTML = order.order_details; // fallback if invalid JSON
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
                    badge.innerText = data.length;
                    badge.style.display = "inline-block";
                }
            })
            .catch(err => console.error("Error fetching orders:", err));
    }

    // Poll every 5 seconds
    setInterval(fetchOrders, 5000);
    fetchOrders(); // initial call
});
