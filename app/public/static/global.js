// Show promo bar dynamically
window.addEventListener("DOMContentLoaded", () => {
  const body = document.body;

  const promoBarWrapper = document.createElement("div");
  promoBarWrapper.innerHTML = `<div class="promo-bar"><p>Store-wide 50% off all November!</p></div>`;
  const promoBar = promoBarWrapper.firstChild;

  body.insertAdjacentElement("afterbegin", promoBar);
});

// Add cart number of items
function syncNCartItems() {
  const USER_ID = localStorage.getItem("user_id") || 1;
  fetch(`../var/main.php?action=get_cart&user_id=${USER_ID}`)
    .then((r) => {
      if (!r.ok) {
        console.error("Issue retrieving cart");
        return;
      }

      r.json()
        .then((dat) => {
          if (!dat.success) {
            console.error("Error retrieving cart");
            return;
          }

          const nCartItems = dat.cart.length;
          document.querySelector("#cart-number-of-items").innerText =
            nCartItems;
        })
        .catch(console.error);
    })
    .catch(console.error);
}
window.addEventListener("DOMContentLoaded", syncNCartItems);
