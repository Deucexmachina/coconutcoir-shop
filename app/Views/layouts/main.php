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
<?php
$cartCount = 0;
if ($sessionUser && ($sessionUser['role'] ?? '') === 'buyer') {
    $db = \Config\Database::connect();
    $row = $db->table('cart_items')
        ->selectSum('quantity', 'qty')
        ->where('user_id', (int) $sessionUser['id'])
        ->get()
        ->getRowArray();
    $cartCount = (int) ($row['qty'] ?? 0);
} else {
    $guestCart = session('guest_cart') ?? [];
    if (is_array($guestCart)) {
        foreach ($guestCart as $qty) {
            $cartCount += (int) $qty;
        }
    }
}
?>
<div class="app-shell">
    <header class="site-header">
        <div class="container header-bar">
            <a href="<?= base_url('/') ?>" class="brand">
                <img src="<?= base_url('assets/images/coconut-logo.svg') ?>" alt="CoirCraft coconut logo">
                <div>
                    <div class="brand-name">CoirCraft Collective</div>
                    <div class="brand-tagline">Eco Coconut Coir Store</div>
                </div>
            </a>
            <div class="header-nav-groups">
                <nav class="nav-links nav-links-left">
                    <a href="<?= base_url('/') ?>">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M12 3l9 8h-3v10h-5v-6H11v6H6V11H3z"/></svg>
                        </span>
                        Home
                    </a>
                    <a href="<?= base_url('/storefront') ?>">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M3 4h18l-1.5 6H4.5L3 4zm2 8h14v8H5v-8z"/></svg>
                        </span>
                        Storefront
                    </a>
                    <a href="<?= base_url('/products') ?>">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M4 6h16v12H4zM7 9h10v2H7zm0 4h6v2H7z"/></svg>
                        </span>
                        Products
                    </a>
                </nav>
                <nav class="nav-links nav-links-right">
                    <?php if ($sessionUser && ($sessionUser['role'] ?? '') === 'seller'): ?>
                        <div class="settings-menu" data-settings-menu>
                            <button type="button" class="settings-trigger" data-settings-trigger>
                                <span class="nav-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M19.14 12.94a7.87 7.87 0 0 0 .05-.94a7.87 7.87 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.63l-1.92-3.32a.5.5 0 0 0-.61-.22l-2.39.96a7.14 7.14 0 0 0-1.63-.94l-.36-2.54A.49.49 0 0 0 13.89 2h-3.78a.49.49 0 0 0-.49.41l-.36 2.54c-.58.22-1.12.53-1.63.94l-2.39-.96a.5.5 0 0 0-.61.22L2.71 8.47a.5.5 0 0 0 .12.63l2.03 1.58a7.87 7.87 0 0 0-.05.94a7.87 7.87 0 0 0 .05.94l-2.03 1.58a.5.5 0 0 0-.12.63l1.92 3.32a.5.5 0 0 0 .61.22l2.39-.96c.51.41 1.05.72 1.63.94l.36 2.54a.49.49 0 0 0 .49.41h3.78a.49.49 0 0 0 .49-.41l.36-2.54c.58-.22 1.12-.53 1.63-.94l2.39.96a.5.5 0 0 0 .61-.22l1.92-3.32a.5.5 0 0 0-.12-.63zM12 15.5A3.5 3.5 0 1 1 12 8.5a3.5 3.5 0 0 1 0 7z"/></svg>
                                </span>
                                Settings
                            </button>
                            <div class="settings-dropdown" data-settings-dropdown>
                                <a href="<?= base_url('/seller/storefront') ?>">Storefront Management</a>
                                <a href="<?= base_url('/seller/inventory') ?>">Inventory Management</a>
                                <a href="<?= base_url('/seller/reports') ?>">Reports</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('/cart') ?>" class="cart-quick-link" aria-label="Cart">
                            <span class="nav-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M7 4h-2l-1 2v2h2l2.6 5.6L7.3 16c-.2.3-.3.6-.3 1a2 2 0 0 0 2 2h10v-2H9.4l.9-1.5h7.4a2 2 0 0 0 1.8-1.1L22 8H8.1L7 6h14V4H7zM9 20a1.5 1.5 0 1 0 0 3a1.5 1.5 0 0 0 0-3zm8 0a1.5 1.5 0 1 0 0 3a1.5 1.5 0 0 0 0-3z"/></svg>
                            </span>
                            <span class="cart-bubble"><?= (int) $cartCount ?></span>
                        </a>
                        <a href="<?= base_url('/profile') ?>">
                            <span class="nav-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5a5 5 0 0 0 5 5zm0 2c-4.4 0-8 2-8 4.5V21h16v-2.5C20 16 16.4 14 12 14z"/></svg>
                            </span>
                            Profile
                        </a>
                    <?php endif; ?>
                    <button type="button" class="theme-toggle-btn" id="themeToggleBtn" aria-label="Toggle dark mode" title="Toggle dark mode">
                        <span class="nav-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M12 2a1 1 0 0 1 1 1v1.6a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1zm0 15a5 5 0 1 0 0-10a5 5 0 0 0 0 10zm9-5a1 1 0 0 1-1 1h-1.6a1 1 0 1 1 0-2H20a1 1 0 0 1 1 1zM6.6 12a1 1 0 0 1-1 1H4a1 1 0 1 1 0-2h1.6a1 1 0 0 1 1 1zm11.32 6.73a1 1 0 0 1-1.42 0l-1.13-1.13a1 1 0 1 1 1.41-1.41l1.14 1.13a1 1 0 0 1 0 1.41zm-9.31-9.32a1 1 0 0 1-1.41 0L6.06 8.28a1 1 0 0 1 1.41-1.41l1.14 1.13a1 1 0 0 1 0 1.41zm9.31-2.54a1 1 0 0 1 0 1.41l-1.14 1.13a1 1 0 1 1-1.41-1.41l1.13-1.13a1 1 0 0 1 1.42 0zM8.61 16.6a1 1 0 0 1 0 1.41l-1.14 1.13a1 1 0 0 1-1.41-1.41l1.14-1.13a1 1 0 0 1 1.41 0z"/></svg>
                        </span>
                    </button>
                    <?php if (! $sessionUser): ?>
                        <a href="<?= base_url('/login') ?>">
                            <span class="nav-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M7 14h2v3h8V7H9v3H7V7a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2zm5-1l-4-4l1.4-1.4L16.8 11H3v2z"/></svg>
                            </span>
                            Login
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('/logout') ?>">
                            <span class="nav-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" width="14" height="14"><path fill="currentColor" d="M10 17l1.4 1.4L17.8 12l-6.4-6.4L10 7l4 4H3v2h11zM21 3h-8v2h8v14h-8v2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"/></svg>
                            </span>
                            Logout
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main class="site-main">
        <div class="container">
            <?= view('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container footer-content footer-grid">
            <section class="footer-col">
                <div class="footer-brand">
                    <img src="<?= base_url('assets/images/coconut-logo.svg') ?>" alt="CoirCraft coconut logo">
                    <strong>CoirCraft Collective</strong>
                </div>
                <p>CoirCraft is a sustainable storefront for coconut-coir home, garden, and agricultural essentials.</p>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M13 9h3V6h-3c-2.2 0-4 1.8-4 4v2H7v3h2v5h3v-5h3l1-3h-4v-2c0-.6.4-1 1-1z"/></svg></a>
                    <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7m5 3a5 5 0 1 1 0 10a5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6a3 3 0 0 0 0-6m4.5-2.7a1.2 1.2 0 1 1 0 2.4a1.2 1.2 0 0 1 0-2.4z"/></svg></a>
                    <a href="#" aria-label="YouTube"><svg viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M23 12s0-3.5-.4-5.1a2.7 2.7 0 0 0-1.9-1.9C19.1 4.6 12 4.6 12 4.6s-7.1 0-8.7.4a2.7 2.7 0 0 0-1.9 1.9C1 8.5 1 12 1 12s0 3.5.4 5.1a2.7 2.7 0 0 0 1.9 1.9c1.6.4 8.7.4 8.7.4s7.1 0 8.7-.4a2.7 2.7 0 0 0 1.9-1.9c.4-1.6.4-5.1.4-5.1zm-13 3.8V8.2l6.3 3.8z"/></svg></a>
                </div>
            </section>
            <section class="footer-col">
                <h4>Contact Us</h4>
                <p>18 Coir Lane, Davao City, Philippines</p>
                <p>+63 917 555 0188</p>
                <p>coircraft@gmail.com</p>
            </section>
            <section class="footer-col">
                <h4>Quick Links</h4>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Refund Policy</a>
                <a href="#">Shipping Policy</a>
                <a href="#">FAQs</a>
            </section>
            <section class="footer-col">
                <h4>Newsletter</h4>
                <form class="newsletter-form" action="#" method="post">
                    <input type="email" placeholder="your@email.com">
                    <button type="submit">Subscribe</button>
                </form>
            </section>
        </div>
        <div class="container footer-disclaimer">
            For educational purposes only, and no copyright infringement is intended.
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
