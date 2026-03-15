<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'CoirCraft') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<?php $cartAdded = session()->getFlashdata('cart_added'); ?>
<?php $sessionUser = session('user'); ?>
<div class="app-shell">
    <header class="site-header">
        <div class="container header-bar">
            <a href="<?= base_url('/') ?>" class="brand">
                <img src="<?= base_url('assets/images/logo.png') ?>" alt="CoirCraft logo">
                <div>
                    <div class="brand-name">CoirCraft Collective</div>
                    <div class="brand-tagline">Eco Coconut Coir Store</div>
                </div>
            </a>
            <nav class="nav-links">
                <a href="<?= base_url('/') ?>">Home</a>
                <?php if (! $sessionUser): ?>
                    <a href="<?= base_url('/login') ?>">Buyer Login</a>
                    <a href="<?= base_url('/register') ?>">Register</a>
                    <a href="<?= base_url('/seller/login') ?>">Seller Login</a>
                <?php elseif ($sessionUser['role'] === 'buyer'): ?>
                    <a href="<?= base_url('/storefront') ?>">Storefront</a>
                    <a href="<?= base_url('/products') ?>">Products</a>
                    <a href="<?= base_url('/cart') ?>">Cart</a>
                    <a href="<?= base_url('/checkout') ?>">Checkout</a>
                    <a href="<?= base_url('/transactions') ?>">Transactions</a>
                    <a href="<?= base_url('/profile') ?>">Profile</a>
                    <a href="<?= base_url('/logout') ?>">Logout</a>
                <?php else: ?>
                    <a href="<?= base_url('/seller/storefront') ?>">Storefront</a>
                    <a href="<?= base_url('/seller/inventory') ?>">Inventory</a>
                    <a href="<?= base_url('/seller/reports') ?>">Reports</a>
                    <a href="<?= base_url('/logout') ?>">Logout</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="site-main">
        <div class="container">
            <?= view('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container footer-content">
            <strong>CoirCraft Collective</strong>
            <div>For educational purposes only, and no copyright infringement is intended.</div>
        </div>
    </footer>
</div>

<?php if ($cartAdded): ?>
    <div class="cart-toast" id="cartToast">
        <div class="cart-toast-content">
            <div class="cart-toast-message">
                <strong>Item added to cart!</strong>
                <?php if (! empty($cartAdded['name'])): ?>
                    <div class="muted"><?= esc($cartAdded['name']) ?></div>
                <?php endif; ?>
            </div>
            <div class="cart-toast-actions">
                <a href="<?= base_url('/cart') ?>" class="btn">View Cart</a>
            </div>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('cartToast');
            if (toast) {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }
        }, 5000);
    </script>
<?php endif; ?>
<script src="<?= base_url('assets/js/app.js') ?>" defer></script>
</body>
</html>
