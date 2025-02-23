function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.querySelector(".main-content");

    // Toggle the 'active' class on the sidebar
    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("expanded");
}


function fillForm(id, name, price, description) {
    document.getElementById('id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('price').value = price;
    document.getElementById('description').value = description;
}
