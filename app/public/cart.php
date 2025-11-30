<?php require_once __DIR__ . "/var/helpers.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart • Grizzly Gear</title>

    <link rel="stylesheet" href="./static/global.css">
    <link rel="stylesheet" href="./static/cart.css">

    <!-- Google Icons -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>

    <script src="./static/global.js"></script>
</head>
<body>

<!-- Navbar -->
<?php echo get_nav_bar(); ?>

<!-- Hero -->
<div class="hero">
    <div class="hero-overlay">
        <h1>Your Cart</h1>
    </div>
</div>

<main class="cart-container">

    <div id="cart-root"></div>

</main>


<script>
// ------------------------------------------------------------
// USER ID
// ------------------------------------------------------------
let appliedPromo = localStorage.getItem("promo_code") || null;
let appliedDiscount = parseFloat(localStorage.getItem("promo_discount") || 0);

let USER_ID = localStorage.getItem("user_id");
if (!USER_ID) {
    USER_ID = 1;
    localStorage.setItem("user_id", 1);
}

// ------------------------------------------------------------
// FETCH INVENTORY
// ------------------------------------------------------------
let inventory = [];

async function loadInventory() {
    const r = await fetch("../var/main.php?action=inventory");
    inventory = await r.json();
}

// ------------------------------------------------------------
// FETCH USER CART
// ------------------------------------------------------------
async function loadCart() {
    const res = await fetch(`../var/main.php?action=get_cart&user_id=${USER_ID}`);
    const data = await res.json();

    if (!data.success) return renderEmptyCart();
    renderCart(data.cart);


    if (appliedPromo) {
        autoValidatePromo(appliedPromo, data.cart);
    }
}

// ------------------------------------------------------------
// VALIDATE PROMO CODE
// ------------------------------------------------------------
async function autoValidatePromo(code, cart) {
    const msg = document.getElementById("promo-msg");
    const promoInput = document.getElementById("promo-code");

    const res = await fetch("../var/main.php?action=validate_promo", {
        method: "POST",
        body: new URLSearchParams({ code })
    });

    const data = await res.json();

    if (!data.valid) {
        // Promo no longer valid → remove it
        appliedPromo = null;
        appliedDiscount = 0;
        localStorage.removeItem("promo_code");
        localStorage.removeItem("promo_discount");

        msg.textContent = "Promo expired or invalid.";
        msg.style.color = "#d9534f";
        updateSummary(cart);
        return;
    }

    // Promo is valid → apply it
    appliedPromo = code.toUpperCase();
    appliedDiscount = parseFloat(data.discount);

    promoInput.value = appliedPromo;

    msg.textContent = `Promo applied automatically: ${appliedPromo} (-${(appliedDiscount * 100).toFixed(0)}%)`;
    msg.style.color = "#2fa54f";

    updateSummary(cart);
}

// ------------------------------------------------------------
// RENDER EMPTY CART
// ------------------------------------------------------------
function renderEmptyCart() {
    document.getElementById("cart-root").innerHTML = `
        <div class="cart-empty">
            <h2>Your cart is empty</h2>
            <p>Browse the shop to add items.</p>
            <a href="shop.php" class="btn primary">Continue Shopping</a>
        </div>
    `;
}

// ------------------------------------------------------------
// RENDER CART
// ------------------------------------------------------------
function renderCart(cart) {
    if (cart.length === 0) return renderEmptyCart();
    window._currentCart = cart;


    let html = `
        <div class="cart-wrapper">

            <!-- LEFT SIDE -->
            <div class="cart-items">
    `;

    cart.forEach(ci => {
        const item = inventory.find(i => i.id == ci.id);
        if (!item) return;

        html += `
        <div class="cart-card">
            <img src="./static/images/Grizzly Gear.png">

            <div class="cart-info">
                <h3>${item.name}</h3>
                <p class="cart-price">$${item.price.toFixed(2)}</p>

                <div class="cart-controls">
                    <input
                        type="number"
                        class="qty-box qty-input"
                        min="1"
                        value="${ci.qty}"
                        data-id="${ci.id}"
                    >
                    <button class="remove-btn" data-id="${ci.id}">
                        Remove
                    </button>
                </div>
            </div>
        </div>`;
    });

    html += `
            </div>

            <!-- RIGHT SIDE -->
            <div class="cart-summary">
                <div class="summary-card">
                    <h2>Order Summary</h2>
                    <div id="summary-box"></div>

                    <div id="promo">
                        <div id="promo-input-wrapper">
                            <input type="text" id="promo-code" placeholder="Promo code…">
                            <button type="button" class="btn promo-apply">Apply</button>
                        </div>
                        <p id="promo-msg"></p>
                    </div>
                    <button class="checkout-btn">
                        Checkout
                    </button>
                </div>
            </div>

        </div>
    `;

    document.getElementById("cart-root").innerHTML = html;
    updateSummary(cart);

    const promoWrapper = document.querySelector("div#promo");
    const promoInput = promoWrapper.querySelector("#promo-code");
    const promoSubmit = promoWrapper.querySelector("button.btn");
    promoSubmit.addEventListener("click", async () => {
        const code = promoInput.value.trim();
        const msg = document.getElementById("promo-msg");

        if (!code) {
            msg.textContent = "Enter a promo code.";
            msg.style.color = "#d9534f";
            return;
        }

        const res = await fetch("../var/main.php?action=validate_promo", {
            method: "POST",
            body: new URLSearchParams({ code })
        });

        const data = await res.json();

        if (!data.valid) {
            appliedPromo = null;
            appliedDiscount = 0;

            localStorage.removeItem("promo_code");
            localStorage.removeItem("promo_discount");

            msg.textContent = "Invalid promo code.";
            msg.style.color = "#d9534f";
            updateSummary(window._currentCart);
            return;
        }

        // Success → save to LS
        appliedPromo = code.toUpperCase();
        appliedDiscount = parseFloat(data.discount);

        localStorage.setItem("promo_code", appliedPromo);
        localStorage.setItem("promo_discount", appliedDiscount.toString());

        msg.textContent = `Promo applied: ${appliedPromo} (-${(appliedDiscount * 100).toFixed(0)}%)`;
        msg.style.color = "#2fa54f";

        updateSummary(window._currentCart);
    });


}

// ------------------------------------------------------------
// UPDATE SUMMARY
// ------------------------------------------------------------
function updateSummary(cart) {
    let subtotal = 0;

    cart.forEach(ci => {
        const item = inventory.find(i => i.id == ci.id);
        if (item) subtotal += item.price * ci.qty;
    });

    const tax = subtotal * 0.13;
    const total = (subtotal + tax) - (subtotal*appliedDiscount);

    document.getElementById("summary-box").innerHTML = `
        <div class="summary-line"><span>Subtotal</span><span>$${subtotal.toFixed(2)}</span></div>
        <div class="summary-line"><span>Discount${appliedPromo ? ` ( <code>${appliedPromo}</code> )` : ""}</span><span>- $${(subtotal*appliedDiscount).toFixed(2)}</span></div>
        <div class="summary-line"><span>Tax (13%)</span><span>$${tax.toFixed(2)}</span></div>
        <div class="summary-total">Total: $${total.toFixed(2)}</div>
    `;
}

// ------------------------------------------------------------
// CHANGE QTY
// ------------------------------------------------------------
document.addEventListener("input", async (e) => {
    if (!e.target.classList.contains("qty-input")) return;

    const newQty = Math.max(1, parseInt(e.target.value));
    const id = e.target.dataset.id;

    await fetch("../var/main.php?action=update_cart_qty", {
        method: "POST",
        body: new URLSearchParams({
            user_id: USER_ID,
            id,
            qty: newQty,
        })
    });

    loadCart();
});

// ------------------------------------------------------------
// REMOVE
// ------------------------------------------------------------
document.addEventListener("click", async (e) => {
    if (!e.target.classList.contains("remove-btn")) return;

    const id = e.target.dataset.id;

    await fetch("../var/main.php?action=remove_from_cart", {
        method: "POST",
        body: new URLSearchParams({
            user_id: USER_ID,
            id,
        })
    });

    loadCart();
    syncNCartItems();
});

// ------------------------------------------------------------
// INIT
// ------------------------------------------------------------
(async function init() {
    await loadInventory();
    loadCart();
})();
</script>

</body>
</html>
