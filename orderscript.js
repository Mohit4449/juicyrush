/* orderscript.js */
document.addEventListener('DOMContentLoaded', () => {
    console.log('Orders page loaded');
});

// orderscript.js
const btnNormal = document.getElementById('btnNormal');
const btnCustom = document.getElementById('btnCustom');
const tablesContainer = document.getElementById('tablesContainer');

function showTable(type) {
  if (!tablesContainer) return;
  if (type === 'normal') {
    tablesContainer.style.transform = 'translateX(0%)';
    btnNormal.classList.add('active');
    btnCustom.classList.remove('active');
    btnNormal.setAttribute('aria-pressed', 'true');
    btnCustom.setAttribute('aria-pressed', 'false');
  } else {
    tablesContainer.style.transform = 'translateX(-50%)';
    btnCustom.classList.add('active');
    btnNormal.classList.remove('active');
    btnCustom.setAttribute('aria-pressed', 'true');
    btnNormal.setAttribute('aria-pressed', 'false');
  }
}

// Attach events
if (btnNormal) btnNormal.addEventListener('click', () => showTable('normal'));
if (btnCustom) btnCustom.addEventListener('click', () => showTable('custom'));

// Initialize on DOM ready (if not set in inline script)
document.addEventListener('DOMContentLoaded', () => {
  // Default view
  showTable('normal');
});
