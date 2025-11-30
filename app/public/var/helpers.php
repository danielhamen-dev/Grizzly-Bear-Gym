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
                </a>
            </div>
        </nav>
    HTML;
}
