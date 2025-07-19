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

