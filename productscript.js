document.addEventListener("DOMContentLoaded", () => {
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

    // Event delegation for Add to Cart buttons
    document.body.addEventListener("click", (event) => {
        if (event.target.classList.contains("add-to-cart")) {
            const card = event.target.closest(".product-card");
            const productName = card.querySelector(".product-card-content h3").innerText;

            // package select (may not exist for some cards)
            const packageSelect = card.querySelector(".package-select");
            const packageSize = packageSelect ? packageSelect.value : "100ml";

            // Prefer the selected option's data-price if available
            let productPrice = 0;
            if (packageSelect && packageSelect.selectedOptions.length > 0) {
                productPrice = parseFloat(packageSelect.selectedOptions[0].getAttribute("data-price"));
            } else {
                const priceElement = card.querySelector(".product-price");
                productPrice = parseFloat(priceElement.innerText.replace("₹", "").trim());
            }

            // product id (if you set data-id on the button or id on card)
            const productId = event.target.getAttribute("data-id") || (card.id ? card.id.replace("product", "") : null);

            const product = {
                id: productId ? parseInt(productId) : null,
                name: productName,
                package: packageSize,
                price: productPrice
            };

            cart.push(product);

            // recalc total from cart (safer than incrementing)
            total = cart.reduce((sum, it) => sum + parseFloat(it.price), 0);

            updateCart();

            Swal.fire({
                position: "top-end",
                width: 400,
                text: `${productName} (${packageSize}) added to cart!`,
                icon: "success",
                showConfirmButton: false,
                timer: 1200,
                showClass: {
                    popup: `
                    animate__animated
                    animate__fadeIn
                    animate__faster
                    `
                },
                hideClass: {
                    popup: `
                    animate__animated
                    animate__fadeOut
                    animate__faster
                    `
                }
            });
        }
    });

    // Update cart UI
    function updateCart() {
        cartItems.innerHTML = "";
        cart.forEach((item, index) => {
            const cartItem = document.createElement("div");
            cartItem.classList.add("cart-item");

            cartItem.innerHTML = `
                <div class="cart-item-details">
                    <h4>${item.name} <small>(${item.package})</small></h4>
                    <p>₹${parseFloat(item.price).toFixed(2)}</p>
                </div>
                <button class="cart-item-remove" onclick="removeFromCart(${index})">X</button>
            `;

            cartItems.appendChild(cartItem);

            setTimeout(() => {
                cartItem.classList.add("show");
            }, 10);
        });

        cartCount.innerText = cart.length;
        cartTotal.innerText = total.toFixed(2);

        bounceCartIcon();
    }

    function bounceCartIcon() {
        cartIcon.classList.add("bounce");
        setTimeout(() => {
            cartIcon.classList.remove("bounce");
        }, 500);
    }

    // Remove item from cart
    window.removeFromCart = (index) => {
        if (index >= 0 && index < cart.length) {
            cart.splice(index, 1);
            total = cart.reduce((sum, it) => sum + parseFloat(it.price), 0);
            updateCart();
        }
    };

    // Checkout: send same shape you used before (orderDetails as JSON string)
    checkoutBtn.addEventListener("click", () => {
        if (cart.length > 0) {
            const orderDetails = JSON.stringify(cart); // now includes package & id
            const totalItems = cart.length;
            const totalAmount = total.toFixed(2);

            fetch("process_order.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ orderDetails, totalItems, totalAmount }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Order Placed!", "Your order was placed successfully!", "success");
                    cart = [];
                    total = 0;
                    updateCart();
                    cartSlider.classList.remove("open");
                } else {
                    Swal.fire("Error!", data.error || "Failed to place order.", "error");
                }
            })
            .catch(err => console.error(err));
        } else {
            Swal.fire("Oops!", "Your cart is empty!", "warning");
        }
    });

    // Update visible price on package change (your existing handler)
    document.querySelectorAll(".package-select").forEach(select => {
        select.addEventListener("change", function () {
            let price = this.selectedOptions[0].getAttribute("data-price");
            let card = this.closest(".product-card");
            card.querySelector(".product-price").textContent = "₹ " + price;
        });
    });
});
