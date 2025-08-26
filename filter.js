document.addEventListener("DOMContentLoaded", function() {
    const categories = document.querySelectorAll(".category-box");
    const productsContainer = document.getElementById("products-container");

    // Function to load products by category
    function loadProducts(category = "") {
        fetch("fetch_products.php?category=" + encodeURIComponent(category))
            .then(response => response.text())
            .then(data => {
                productsContainer.innerHTML = data;
            })
            .catch(error => console.error("Error loading products:", error));
    }

    // Load all products on first load
    loadProducts();

    // Add click event to each category
    categories.forEach(box => {
        box.addEventListener("click", () => {
            const category = box.dataset.category;
            loadProducts(category);
        });
    });
});

