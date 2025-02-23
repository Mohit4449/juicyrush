document.addEventListener("DOMContentLoaded", () => {
    const productCards = document.querySelectorAll(".product-card");
    const cartIcon = document.getElementById("cartIcon");
    const cartSlider = document.getElementById("cartSlider");
    const closeCart = document.getElementById("closeCart");
    const cartItems = document.getElementById("cartItems");
    const cartCount = document.getElementById("cartCount");
    const cartTotal = document.getElementById("cartTotal");
    const checkoutBtn = document.getElementById("checkoutBtn");

    let cart = [];
    let total = 0;

    // Toggle cart slider
    cartIcon.addEventListener("click", () => {
        cartSlider.classList.toggle("open");
    });

    closeCart.addEventListener("click", () => {
        cartSlider.classList.remove("open");
    });

    // Drawer effect for product cards
    productCards.forEach((card) => {
        const drawer = card.querySelector(".product-card-drawer");

        card.addEventListener("click", (event) => {
            if (event.target.classList.contains("add-to-cart")) {
                return;
            }

            if (drawer.style.bottom === "0px") {
                drawer.style.bottom = "-100%";
            } else {
                drawer.style.bottom = "0px";
            }
        });
    });

    // Add to cart functionality
    productCards.forEach((card) => {
        const addToCartBtn = card.querySelector(".add-to-cart");
        const productName = card.querySelector("h3").innerText;
        const productPrice = parseFloat(card.querySelector(".price").innerText.replace("₹", "").trim());

        addToCartBtn.addEventListener("click", () => {
            const product = {
                name: productName,
                price: productPrice,
            };

            cart.push(product);
            total += productPrice;
            updateCart();
        });
    });

    // Update cart UI
    function updateCart() {
        cartItems.innerHTML = "";
        cart.forEach((item, index) => {
            const cartItem = document.createElement("div");
            cartItem.classList.add("cart-item");

            cartItem.innerHTML = `
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p>₹${item.price.toFixed(2)}</p>
                </div>
                <button class="cart-item-remove" onclick="removeFromCart(${index})">×</button>
            `;

            cartItems.appendChild(cartItem);

            // Trigger the fade-in effect
            setTimeout(() => {
                cartItem.classList.add("show");
            }, 10);
        });

        cartCount.innerText = cart.length;
        cartTotal.innerText = total.toFixed(2);

        // Bounce the cart icon
        bounceCartIcon();
    }

    // Bounce effect for the cart icon
    function bounceCartIcon() {
        cartIcon.classList.add("bounce");
        setTimeout(() => {
            cartIcon.classList.remove("bounce");
        }, 500);
    }

    // Remove item from cart
    window.removeFromCart = (index) => {
        total -= cart[index].price;
        cart.splice(index, 1);
        updateCart();
    };

    // Checkout functionality
    // Checkout functionality
checkoutBtn.addEventListener("click", () => {
    if (cart.length > 0) {
        const username = prompt("Enter your username:"); // Temporary way to get username (replace with actual authentication)

        if (username) {
            const orderDetails = JSON.stringify(cart);
            const totalItems = cart.length;
            const totalAmount = total.toFixed(2);

            // Send order details to the server via AJAX
            fetch("process_order.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    username: username,
                    orderDetails: orderDetails,
                    totalItems: totalItems,
                    totalAmount: totalAmount,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert("Order placed successfully!");
                        cart = [];
                        total = 0;
                        updateCart();
                        cartSlider.classList.remove("open");
                    } else {
                        alert("Failed to place order. Please try again.");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        }
    } else {
        alert("Your cart is empty!");
    }
});

});