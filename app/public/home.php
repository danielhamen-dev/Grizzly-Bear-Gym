<?php require_once __DIR__ . "/var/helpers.php"; ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Home &bull; Grizzly Bear Gym</title>
        <link rel="stylesheet" href="./static/global.css" />
        <link rel="stylesheet" href="./static/home.css" />

        <!--Google Icons-->
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        />
        <script src="static/global.js"></script>
    </head>
    <body>
        <button
            onclick="openModal()"
            style="position: fixed; bottom: 12px; right: 12px; z-index: 2000"
        >
            Open signup modal
        </button>
        <!-- Signup Modal -->
        <div class="modal-overlay" id="signupOverlay" hidden></div>

        <div
            class="modal"
            id="signupModal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="signupTitle"
            aria-describedby="signupDesc"
            hidden
        >
            <button class="modal-close" id="signupClose" aria-label="Close">
                &times;
            </button>

            <h2 id="signupTitle">Join The Grizzly Den</h2>
            <p id="signupDesc">
                Create an account for early drops, exclusive deals, and free
                shipping.
            </p>

            <form id="signupForm" novalidate>
                <label class="sr-only" for="signupEmail">Email</label>
                <input
                    type="email"
                    id="signupEmail"
                    name="email"
                    placeholder="you@example.com"
                    required
                    aria-required="true"
                />

                <label class="sr-only" for="signupPassword">Password</label>
                <input
                    type="password"
                    id="signupPassword"
                    name="password"
                    placeholder="Create a password"
                    required
                    aria-required="true"
                />

                <button type="submit" class="btn primary">
                    Create account
                </button>
                <button type="button" class="btn ghost" id="continueGuest">
                    Continue as guest
                </button>

                <div class="helper">
                    Already have an account? <a href="/login">Sign in</a>
                </div>

                <div
                    class="form-error"
                    id="signupError"
                    aria-live="polite"
                ></div>
            </form>
        </div>

        <section id="home" class="hero-section">
            <!--navbar-->
            <?php echo get_nav_bar(); ?>

            <!-- HERO IMAGE + TEXT -->
            <div class="hero-content">
                <h1 class="hero-title">Strength that stands tall</h1>
                <p class="hero-subtitle">
                    Black Friday All Month | Gear Up & Save Big!
                </p>
                <a href="#best-sellers" class="hero-btn">Shop Now</a>
            </div>
        </section>

        <section id="best-sellers" class="best-sellers featured-section">
            <h2>Best Sellers</h2>
            <div class="home-item-list">
            </div>
            <a href="shop.php" class="view-more">View More</a>
        </section>

        <section id="equipment" class="featured-section">
            <!-- Equipment section -->
            <h2>Our Equipment</h2>
            <div class="home-item-list"></div>
            <a href="shop.php" class="view-more">View More</a>
        </section>

        <section id="supplements" class="featured-section">
            <!-- Supplements section -->
            <h2>Supplements</h2>
            <div class="home-item-list"></div>
            <a href="shop.php" class="view-more">View More</a>
        </section>

        <section id="accessories" class="featured-section">
            <!-- Accessories section -->
            <h2>Accessories</h2>
            <div class="home-item-list"></div>
            <a href="shop.php" class="view-more">View More</a>
        </section>

        <section id="apparel" class="featured-section">
            <!-- Apparel section -->
            <h2>Apparel</h2>
            <div class="home-item-list"></div>
            <a href="shop.php" class="view-more">View More</a>
        </section>

        <section id="about" class="info-section featured-section">
            <h2>About Us</h2>
            <p>Grizzly Gear is built for athletes who demand durability, performance, and style. We create equipment and apparel that stands up to real training.</p>
        </section>

        <section id="contact" class="info-section featured-section">
            <h2>Contact</h2>
            <p>Have a question? Reach out at <a href="mailto:support@grizzlygear.com">support@grizzlygear.com</a>.</p>
        </section>


        <?php echo get_footer_bar(); ?>

        <script src="static/home.js"></script>
    </body>
</html>
