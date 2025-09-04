const checkboxes = document.querySelectorAll("input[type=checkbox]");
const sizeRadios = document.querySelectorAll("input[name=size]");
const totalEl = document.getElementById("total");

function updateTotal() {
  let total = 0;

  checkboxes.forEach(cb => {
    if (cb.checked) total += parseFloat(cb.dataset.price);
  });

  let size = document.querySelector("input[name=size]:checked").value;
  if (size == 500) total += 30;
  if (size == 1000) total += 60;

  totalEl.textContent = total;
}

checkboxes.forEach(cb => cb.addEventListener("change", updateTotal));
sizeRadios.forEach(r => r.addEventListener("change", updateTotal));
