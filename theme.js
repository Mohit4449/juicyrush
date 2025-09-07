// Elements
const sunIcon = document.querySelector(".sun");
const moonIcon = document.querySelector(".moon");
const themeSwitchCheckbox = document.getElementById("themeSwitch");

// Theme Vars
const userTheme = localStorage.getItem("theme");
const systemTheme = window.matchMedia("(prefers-color-scheme: dark)").matches;

// Icon + Checkbox Sync
const updateUI = () => {
  if (document.documentElement.classList.contains("dark")) {
    themeSwitchCheckbox.checked = true;
    sunIcon.classList.add("display-none");
    moonIcon.classList.remove("display-none");
  } else {
    themeSwitchCheckbox.checked = false;
    moonIcon.classList.add("display-none");
    sunIcon.classList.remove("display-none");
  }
};

// Initial Theme Check
const themeCheck = () => {
  if (userTheme === "dark" || (!userTheme && systemTheme)) {
    document.documentElement.classList.add("dark");
  } else {
    document.documentElement.classList.remove("dark");
  }
  updateUI();
};

// Manual Theme Switch
const themeSwitch = () => {
  if (document.documentElement.classList.contains("dark")) {
    document.documentElement.classList.remove("dark");
    localStorage.setItem("theme", "light");
  } else {
    document.documentElement.classList.add("dark");
    localStorage.setItem("theme", "dark");
  }
  updateUI();
};

// Event Listener for Checkbox
themeSwitchCheckbox.addEventListener("change", themeSwitch);

// Run on Load
themeCheck();
