<?php require_once __DIR__ . "/var/helpers.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product • Grizzly Gear</title>

    <link rel="stylesheet" href="./static/global.css">
    <link rel="stylesheet" href="./static/product.css">

    <!-- Google Icons -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>

    <script src="./static/global.js"></script>
</head>
<body>

<?php echo get_nav_bar(); ?>

<!-- HERO -->
<div class="hero">
    <div class="hero-overlay">
        <h1>Product Details</h1>
    </div>
</div>

<main id="product-root" class="product-container">
    <!-- JS injects product here -->
</main>

<script>
const PROD_ID = new URLSearchParams(window.location.search).get("id");
let USER_ID = localStorage.getItem("user_id") || 1;

// Force save user 1 if missing
localStorage.setItem("user_id", USER_ID);

// Global inventory array
let inventory = [];

/* -----------------------------
   Load inventory from backend
------------------------------ */
async function loadInventory() {
    const r = await fetch("../var/main.php?action=inventory");
    inventory = await r.json();
}

/* -----------------------------
   Render Product Page
------------------------------ */
function renderProduct(item) {
    if (!item) {
        document.getElementById("product-root").innerHTML = `
            <div class="product-error">
                <h2>Product Not Found</h2>
                <a href="shop.php" class="btn primary">Back to Shop</a>
            </div>
        `;
        return;
    }

    document.getElementById("product-root").innerHTML = `
        <div class="product-wrapper">

            <!-- LEFT: Image -->
            <div class="product-image">
                <img src="./static/images/Grizzly Gear.png" alt="${item.name}">
            </div>

            <!-- RIGHT: Info -->
            <div class="product-info">
                <h2>${item.name}</h2>

                <p class="prod-price">$${item.price.toFixed(2)}</p>

                <p class="prod-stock">Stock: ${item.stock}</p>

                <div class="prod-tags">
                    ${item.tags.map(t => `<span class="tag">${t}</span>`).join("")}
                </div>

                <p class="prod-desc">
                    This is premium Grizzly Gear quality—durable, comfortable,
                    and designed for elite performance. Perfect for training,
                    everyday wear, or competition.
                </p>

                <div class="prod-actions">
                    <input type="number" id="qty" min="1" value="1">
                    <button class="btn primary" id="addToCartBtn">
                        Add to Cart
                    </button>
                </div>
            </div>

        </div>
    `;

    // Add to Cart
    document.getElementById("addToCartBtn").addEventListener("click", async () => {
        const qty = Math.max(1, parseInt(document.getElementById("qty").value));

        await fetch("../var/main.php?action=add_to_cart", {
            method: "POST",
            body: new URLSearchParams({
                user_id: USER_ID,
                id: item.id,
                qty
            })
        });

        alert("Added to cart!");
        syncNCartItems();
    });
}

/* -----------------------------
   INIT
------------------------------ */
(async function init() {
    await loadInventory();
    const item = inventory.find(x => x.id == PROD_ID);
    renderProduct(item);
})();
</script>

<?php echo get_footer_bar(); ?>
</body>
</html>
