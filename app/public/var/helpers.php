<?php
function get_nav_bar()
{
    return <<<HTML
        <nav class="navbar">
            <ul class="nav-left">
                <li><a href="home.php">Home</a></li>
                <li><a href="shop.php?tag=equipment">Equipment</a></li>
                <li><a href="shop.php?tag=supplements">Supplements</a></li>
                <li><a href="shop.php?tag=accessory">Accessories</a></li>
                <li><a href="shop.php?tag=apparel">Apparel</a></li>
            </ul>

            <div class="logo-center">
                <img
                    src="static/images/Grizzly Gear.png"
                    alt="The Grizzly Den Logo"
                />
            </div>

            <div class="nav-right">
                <a href="cart.php" class="cart-link">
                    <span class="icon" alt="Shopping Cart">
                        shopping_cart
                    </span>
                    <span id="cart-number-of-items" class="cart-items">-</span>
                </a>
            </div>
        </nav>
    HTML;
}

function get_footer_bar()
{
    return <<<HTML
    <footer class="site-footer">
        <div class="footer-inner">

            <div class="footer-col footer-brand">
                <img src="./static/images/Grizzly Gear.png" class="footer-logo" alt="Grizzly Gear">
                <p class="footer-desc">
                    "Strength that stands tall. Premium gym equipment, supplements, and apparel.
                    Join the community and level up your grind." (ChatGPT)
                </p>

                <div class="footer-social">
                    <span class="icon">fitness_center</span>
                    <span class="icon">smartphone</span>
                    <span class="icon">groups</span>
                </div>
            </div>

            <div class="footer-col">
                <h4>Explore</h4>
                <ul class="footer-links">
                    <li><a href="/home.php">Home</a></li>
                    <li><a href="/shop.php?tag=equipment">Equipment</a></li>
                    <li><a href="/shop.php?tag=supplements">Supplements</a></li>
                    <li><a href="/shop.php?tag=accessories">Accessories</a></li>
                    <li><a href="/shop.php?tag=apparel">Apparel</a></li>
                    <li><a href="/about.php">About Us</a></li>
                    <li><a href="/contact.php">Contact</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Account</h4>
                <form id="footer-auth-form">
                    <input type="email" id="footerEmail" placeholder="Email">
                    <input type="password" id="footerPassword" placeholder="Password">

                    <button type="submit" class="btn primary footer-auth-btn">Sign In</button>
                    <button type="button" class="btn ghost footer-auth-btn" id="footerSignup">
                        Create Account
                    </button>

                    <p class="footer-form-msg" id="footerAuthMsg"></p>
                </form>
            </div>

        </div>

        <div class="footer-bottom">
            <p>&copy; 2025 Grizzly Gear. All rights reserved.</p>
        </div>
    </footer>

    HTML;
}
