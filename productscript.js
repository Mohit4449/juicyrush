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
            const priceElement = card.querySelector(".product-price");
            const productPrice = parseFloat(priceElement.innerText.replace("₹", "").trim());

            const product = {
                name: productName,
                price: productPrice,
            };

            cart.push(product);
            total += productPrice;
            updateCart();

            Swal.fire({
                title: "Success!",
                text: `${productName} added to cart!`,
                icon: "success",
                confirmButtonText: "OK"
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
                    <h4>${item.name}</h4>
                    <p>₹${item.price.toFixed(2)}</p>
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

    window.removeFromCart = (index) => {
        total -= cart[index].price;
        cart.splice(index, 1);
        updateCart();
    };

    checkoutBtn.addEventListener("click", () => {
        if (cart.length > 0) {
            const orderDetails = JSON.stringify(cart);
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
});
