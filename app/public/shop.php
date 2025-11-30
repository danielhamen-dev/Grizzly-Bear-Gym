<?php
require_once __DIR__ . "/var/helpers.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grizzly Gear Shop</title>
    <link rel="stylesheet" href="./static/global.css">
    <link rel="stylesheet" href="./static/shop.css">


    <!--Google Icons-->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
    />
    <script src="static/global.js"></script>
</head>

<body>

    <!--navbar-->
    <?php echo get_nav_bar(); ?>

    <!-- ----------------------------------------------------- -->
    <!-- Hero Header (matches Home style) -->
    <!-- ----------------------------------------------------- -->
    <div class="hero">
        <div class="hero-overlay">
            <h1>Shop Grizzly Gear</h1>
        </div>
    </div>
    <main id="shop-content">


        <!-- search -->
        <div class="search-row">
            <input id="searchBox" type="text" placeholder="Search products…">
        </div>

        <div class="shop-details">
            <aside class="shop-prefs">
                <!-- tags -->
                <h3 class="shop-title">Sort by<h3>
                <select id="sort-by">
                        <option value="relevance">Relevance</option>
                        <option value="price-high">Price (High to Low)</option>
                        <option value="price-low">Price (Low to High)</option>
                </select>
                <h3 class="shop-title">Categories<h3>
                <div class="tags-row"></div>
            </aside>
            <div class="shop-container">
                <!-- items -->
                <div class="item-grid" id="itemGrid"></div>
            </div>
        </div>
    </main>

    <script>
    // -----------------------------------------------------------
    // URL helpers
    // -----------------------------------------------------------
    let fullInventory = [];
    let tagList = {};
    let activeTags = new Set();
    let activeSort = "relevance"; // default

    // Read tags & sort from URL
    function initFromURL() {
        const url = new URL(window.location.href);

        // TAGS
        const tagParam = url.searchParams.get("tag");
        if (tagParam) {
            tagParam.split(",").forEach(t => activeTags.add(t.trim()));
        }

        // SORT
        const sortParam = url.searchParams.get("sort");
        if (sortParam) {
            activeSort = sortParam;
        }

        // Update <select> UI to reflect URL
        const sortSelect = document.getElementById("sort-by");
        sortSelect.value = activeSort;
    }

    // Push updated tag & sort query to URL
    function updateURL() {
        const url = new URL(window.location.href);

        // TAGS
        if (activeTags.size === 0) {
            url.searchParams.delete("tag");
        } else {
            url.searchParams.set("tag", [...activeTags].join(","));
        }

        // SORT
        if (activeSort === "relevance") {
            url.searchParams.delete("sort");
        } else {
            url.searchParams.set("sort", activeSort);
        }

        window.history.replaceState({}, "", url);
    }

    // -----------------------------------------------------------
    // Build category tags from inventory
    // -----------------------------------------------------------
    function populateTags() {
        for (let item of fullInventory) {
            item.tags.forEach(tag => {
                let display = tag.charAt(0).toUpperCase() + tag.slice(1);
                tagList[tag] = display;
            });
        }

        const tagRow = document.querySelector(".tags-row");
        Object.entries(tagList).forEach(([tagId, tagName]) => {
            const tagEl = document.createElement("div");
            tagEl.classList.add("tag");

            const createIcon = (name="", classes=[]) => {
              const c = document.createElement("span");
              c.innerText = name;
              ["icon", ...classes].forEach(cls => c.classList.add(cls));
              return c;
            }
            const tagIconON = createIcon("check_box", ["enabled"]);
            const tagIconOFF = createIcon("check_box_outline_blank", ["disabled"]);

            tagEl.appendChild(tagIconON);
            tagEl.appendChild(tagIconOFF);

            tagEl.dataset.tag = tagId;

            // tag innertext
            const tagText = document.createElement("span");
            tagText.textContent = tagName;
            tagEl.appendChild(tagText);

            // initial UI state
            if (activeTags.has(tagId)) tagEl.classList.add("active");

            // click behaviour
            tagEl.addEventListener("click", () => {
                if (activeTags.has(tagId)) {
                    activeTags.delete(tagId);
                } else {
                    activeTags.add(tagId);
                }

                syncTagUI();
                updateURL();
                renderItems();
            });

            tagRow.appendChild(tagEl);
        });
    }

    function syncTagUI() {
        document.querySelectorAll(".tag").forEach(el => {
            const tag = el.dataset.tag;
            el.classList.toggle("active", activeTags.has(tag));
        });
    }

    // -----------------------------------------------------------
    // Sorting function
    // -----------------------------------------------------------
    function applySorting(list) {
        if (activeSort === "price-high") {
            return list.sort((a, b) => b.price - a.price);
        }
        if (activeSort === "price-low") {
            return list.sort((a, b) => a.price - b.price);
        }
        // relevance = no sorting (default order from JSON)
        return list;
    }

    // -----------------------------------------------------------
    // Render
    // -----------------------------------------------------------
    function renderItems() {
        const grid = document.getElementById("itemGrid");
        grid.innerHTML = "";

        const searchText = document.getElementById("searchBox").value.toLowerCase();

        let result = fullInventory.filter(item => {
            const matchesSearch = item.name.toLowerCase().includes(searchText);
            const matchesTags =
                activeTags.size === 0 ||
                [...activeTags].some(tag => item.tags.includes(tag));

            return matchesSearch && matchesTags;
        });

        // Apply sort here
        result = applySorting(result);

        if (result.length === 0) {
            grid.innerHTML = "<p>No products found.</p>";
            return;
        }

        for (let item of result) {
            const card = document.createElement("div");
            card.className = "item-card";
            card.innerHTML = `
              <a href="product.php?id=${item.id}">
                  <img src="./static/images/Grizzly Gear.png" alt="">
                  <h3>${item.name}</h3>
                  <p>
                    <span ${item.sale_price ? "class='old-price'" : ""}>${item.price.toFixed(2)}</span>
                    ${item.sale_price ? `<span class="sale-price">${item.sale_price.toFixed(2)}</span>` : ""}
                  </p>
                  <p>Stock: ${item.stock}</p>
                  <button onclick="addToCart(${item.id})">Add to Cart</button>
              </a>
            `;
            grid.appendChild(card);
        }
    }

    // -----------------------------------------------------------
    // Select: Sort By
    // -----------------------------------------------------------
    document.getElementById("sort-by").addEventListener("change", (e) => {
        activeSort = e.target.value;
        updateURL();
        renderItems();
    });

    // -----------------------------------------------------------
    // Search
    // -----------------------------------------------------------
    document.getElementById("searchBox").addEventListener("input", renderItems);

    // -----------------------------------------------------------
    // Add to Cart
    // -----------------------------------------------------------
    function addToCart(id) {
      const USER_ID = localStorage.getItem("user_id") || 1;

      fetch("../var/main.php?action=add_to_cart", {
          method: "POST",
          body: new URLSearchParams({
              user_id: USER_ID,
              id: id,
              qty: 1
          })
      })
      .then(r => r.json())
      .then(data => {
          if (data.success) {
              alert("Successfully added to cart!");
          } else {
              alert("Error: " + data.msg);
          }
      }).finally(() => syncNCartItems());
   }


    // -----------------------------------------------------------
    // Init
    // -----------------------------------------------------------
    async function loadInventory() {
        initFromURL();

        const r = await fetch("../var/main.php?action=inventory");
        fullInventory = await r.json();

        populateTags();
        syncTagUI();
        renderItems();
    }

    loadInventory();
    </script>



    <?php echo get_footer_bar(); ?>
</body>
</html>
