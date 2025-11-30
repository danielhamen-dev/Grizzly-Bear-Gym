<?php
// shop.php — complete shop frontend
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grizzly Gear Shop</title>
    <link rel="stylesheet" href="./static/global.css">


    <!--Google Icons-->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
    />
    <script src="static/global.js"></script>
</head>

<body>
    <!--<section id="home" class="hero-section">-->
        <!-- NAVBAR OVER HERO IMAGE -->
        <nav class="navbar">
            <ul class="nav-left">
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php#equipment">Equipment</a></li>
                <li><a href="shop.php#supplements">Supplements</a></li>
                <li><a href="shop.php#accessories">Accessories</a></li>
                <li><a href="shop.php#apparel">Apparel</a></li>
            </ul>

            <div class="logo-center">
                <img
                    src="static/images/Grizzly Gear.png"
                    alt="The Grizzly Den Logo"
                />
            </div>

            <div class="nav-right">
                <a href="#cart" class="cart-link">
                    <span class="icon" alt="Shopping Cart">
                        shopping_cart
                    </span>
                </a>
            </div>
        </nav>
    <!--</section>-->

    <!-- ----------------------------------------------------- -->
    <!-- Hero Header (matches Home style) -->
    <!-- ----------------------------------------------------- -->
    <div class="hero">
        <div class="hero-overlay">
            <h1>Shop Grizzly Gear</h1>
        </div>
    </div>

    <div class="shop-container">

        <!-- search -->
        <div class="search-row">
            <input id="searchBox" type="text" placeholder="Search products…">
        </div>

        <!-- tags -->
        <div class="tags-row">
            <div class="tag" data-tag="shirt">Shirts</div>
            <div class="tag" data-tag="hoodie">Hoodies</div>
            <div class="tag" data-tag="accessory">Accessories</div>
            <div class="tag" data-tag="equipment">Equipment</div>
        </div>

        <!-- items -->
        <div class="item-grid" id="itemGrid"></div>
    </div>

    <script>
    // -----------------------------------------------------------
    // URL + tag helpers
    // -----------------------------------------------------------

    let fullInventory = [];
    let activeTags = new Set();

    // Read tags from ?tag=accessories,equipment
    function initTagsFromURL() {
        const url = new URL(window.location.href);
        const tagParam = url.searchParams.get("tag");

        if (!tagParam) return;

        tagParam.split(",").forEach(t => {
            const tag = t.trim();
            if (tag) activeTags.add(tag);
        });
    }

    // Write tags back to ?tag=... in URL (no reload)
    function updateURLFromTags() {
        const url = new URL(window.location.href);

        if (activeTags.size === 0) {
            url.searchParams.delete("tag");
        } else {
            url.searchParams.set("tag", [...activeTags].join(","));
        }

        window.history.replaceState({}, "", url);
    }

    // Highlight tag buttons based on activeTags
    function syncTagUI() {
        document.querySelectorAll(".tag").forEach(tagEl => {
            const tag = tagEl.dataset.tag;
            if (activeTags.has(tag)) {
                tagEl.classList.add("active");
            } else {
                tagEl.classList.remove("active");
            }
        });
    }

    // -----------------------------------------------------------
    // Load inventory
    // -----------------------------------------------------------

    async function loadInventory() {
        initTagsFromURL();        // set activeTags from URL first

        const r = await fetch("../var/main.php?action=inventory");
        fullInventory = await r.json();

        syncTagUI();
        renderItems();
    }

    // -----------------------------------------------------------
    // Render items
    // -----------------------------------------------------------

    function renderItems() {
        const grid = document.getElementById("itemGrid");
        grid.innerHTML = "";

        const searchText = document.getElementById("searchBox").value.toLowerCase();

        const show = fullInventory.filter(item => {
            // Search
            const matchesSearch = item.name.toLowerCase().includes(searchText);

            // Tags
            const matchesTags =
                activeTags.size === 0 ||
                [...activeTags].some(tag => item.tags.includes(tag));

            return matchesSearch && matchesTags;
        });

        if (show.length === 0) {
            grid.innerHTML = "<p>No products found.</p>";
            return;
        }

        for (let item of show) {
            const card = document.createElement("div");
            card.className = "item-card";

            const imgSrc = "./static/images/Grizzly Gear.png";

            card.innerHTML = `
                <img src="${imgSrc}" alt="Item">
                <h3>${item.name}</h3>
                <p>$${item.price.toFixed(2)}</p>
                <p>Stock: ${item.stock}</p>
                <button onclick="addToCart(${item.id})">Add to Cart</button>
            `;

            grid.appendChild(card);
        }
    }

    // -----------------------------------------------------------
    // Tag click handlers
    // -----------------------------------------------------------

    document.querySelectorAll(".tag").forEach(tagEl => {
        tagEl.addEventListener("click", () => {
            const tag = tagEl.dataset.tag;

            if (activeTags.has(tag)) {
                activeTags.delete(tag);
            } else {
                activeTags.add(tag);
            }

            syncTagUI();
            updateURLFromTags();
            renderItems();
        });
    });

    // -----------------------------------------------------------
    // Search
    // -----------------------------------------------------------

    document.getElementById("searchBox").addEventListener("input", renderItems);

    // -----------------------------------------------------------
    // Add to Cart
    // -----------------------------------------------------------

    function addToCart(id) {
        alert("Item " + id + " added to cart (demo mode)");
    }

    // -----------------------------------------------------------
    // Init page
    // -----------------------------------------------------------

    loadInventory();
    </script>


</body>
</html>
