// Show promo bar dynamically
window.addEventListener("DOMContentLoaded", () => {
  const body = document.body;

  const promoBarWrapper = document.createElement("div");
  promoBarWrapper.innerHTML = `<div class="promo-bar"><p>Store-wide 50% off all November!</p></div>`;
  const promoBar = promoBarWrapper.firstChild;

  body.insertAdjacentElement("afterbegin", promoBar);
});
