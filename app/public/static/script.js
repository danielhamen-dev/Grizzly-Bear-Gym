// scripts.js

// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();

    document.querySelector(this.getAttribute("href")).scrollIntoView({
      behavior: "smooth",
    });
  });
});

// ---- Modal elements ----
const overlay = document.getElementById("signupOverlay");
const modal = document.getElementById("signupModal");
const form = document.getElementById("signupForm");
const email = document.getElementById("signupEmail");
const password = document.getElementById("signupPassword");
const errorBox = document.getElementById("signupError");
const closeBtn = document.getElementById("signupClose");
const guestBtn = document.getElementById("continueGuest");

let lastFocused = null;

// ---- Modal logic ----
function openModal() {
  lastFocused = document.activeElement;
  overlay.hidden = false;
  modal.hidden = false;

  // force reflow to apply transition classes
  void overlay.offsetHeight;

  overlay.classList.add("open");
  modal.classList.add("open");

  // focus first field
  setTimeout(() => email.focus(), 50);
  trapFocus(true);
}

function closeModal() {
  overlay.classList.remove("open");
  modal.classList.remove("open");

  setTimeout(() => {
    overlay.hidden = true;
    modal.hidden = true;
    trapFocus(false);
    if (lastFocused && typeof lastFocused.focus === "function") {
      lastFocused.focus();
    }
  }, 180);
}

// Focus trap (basic)
function onKeydown(e) {
  if (e.key === "Escape") {
    closeModal();
    return;
  }
  if (e.key !== "Tab") return;

  const focusables = modal.querySelectorAll(
    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
  );
  if (!focusables.length) return;

  const first = focusables[0];
  const last = focusables[focusables.length - 1];

  if (e.shiftKey && document.activeElement === first) {
    last.focus();
    e.preventDefault();
  } else if (!e.shiftKey && document.activeElement === last) {
    first.focus();
    e.preventDefault();
  }
}

function trapFocus(enable) {
  const method = enable ? "addEventListener" : "removeEventListener";
  document[method]("keydown", onKeydown);
}

// Show on load every time
document.addEventListener("DOMContentLoaded", () => {
  openModal();
});

// Close interactions
overlay.addEventListener("click", closeModal);
closeBtn.addEventListener("click", closeModal);
guestBtn.addEventListener("click", closeModal);

// Form submit (mock)
form.addEventListener("submit", (e) => {
  e.preventDefault();
  errorBox.textContent = "";

  const emailVal = email.value.trim();
  const passVal = password.value.trim();

  if (!/^\S+@\S+\.\S+$/.test(emailVal)) {
    errorBox.textContent = "Please enter a valid email.";
    email.focus();
    return;
  }
  if (passVal.length < 6) {
    errorBox.textContent = "Password must be at least 6 characters.";
    password.focus();
    return;
  }

  // Fake success
  form.innerHTML = "<p>Account created! Redirecting…</p>";
  setTimeout(() => {
    closeModal();
  }, 800);
});

window.addEventListener("scroll", () => {
  const header = document.querySelector("header");
  if (window.scrollY > 50) {
    header.classList.add("shrink-logo");
  } else {
    header.classList.remove("shrink-logo");
  }
});

window.addEventListener("scroll", () => {
  const header = document.querySelector("header");

  if (window.scrollY > 50) {
    header.classList.add("shrink-logo", "scrolled");
  } else {
    header.classList.remove("shrink-logo", "scrolled");
  }
});
