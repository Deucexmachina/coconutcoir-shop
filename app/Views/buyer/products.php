<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="storefront-benefits-strip">
    <div class="benefit-item">
        <svg viewBox="0 0 24 24" width="15" height="15"><path fill="currentColor" d="M7 18c-1.1 0-2-.9-2-2V5h11l3 3v8c0 1.1-.9 2-2 2H7zm0-2h10V9h-3V6H7v10z"/></svg>
        Returns eligible within 15 days of delivery.
    </div>
    <div class="benefit-item">
        <svg viewBox="0 0 24 24" width="15" height="15"><path fill="currentColor" d="M12 2a8 8 0 0 1 8 8c0 5.3-8 12-8 12S4 15.3 4 10a8 8 0 0 1 8-8m0 10a2 2 0 1 0 0-4a2 2 0 0 0 0 4z"/></svg>
        Nationwide shipping available.
    </div>
    <div class="benefit-item">
        <svg viewBox="0 0 24 24" width="15" height="15"><path fill="currentColor" d="M3 7h18v10H3V7zm2 2v6h14V9H5zm1 8h12v2H6v-2z"/></svg>
        Payment flexibility through Shop Pay.
    </div>
</section>

<section class="storefront-catalog-layout">
    <aside class="storefront-filter-panel">
        <h3>Filters</h3>
        <form method="get" action="<?= base_url('/products') ?>">
            <label class="filter-dot-option">
                <input type="checkbox" name="filters[]" value="featured" <?= in_array('featured', $filters ?? [], true) ? 'checked' : '' ?>>
                <span>Featured Picks</span>
            </label>
            <label class="filter-dot-option">
                <input type="checkbox" name="filters[]" value="trending" <?= in_array('trending', $filters ?? [], true) ? 'checked' : '' ?>>
                <span>Trending</span>
            </label>
            <label class="filter-dot-option">
                <input type="checkbox" name="filters[]" value="best_seller" <?= in_array('best_seller', $filters ?? [], true) ? 'checked' : '' ?>>
                <span>Best Sellers</span>
            </label>
            <label class="filter-dot-option">
                <input type="checkbox" name="filters[]" value="in_stock" <?= in_array('in_stock', $filters ?? [], true) ? 'checked' : '' ?>>
                <span>In Stock</span>
            </label>
            <button type="submit">Apply Filters</button>
        </form>
    </aside>

    <div class="storefront-main-panel">
        <form method="get" action="<?= base_url('/products') ?>" class="storefront-tools">
            <?php foreach (($filters ?? []) as $activeFilter): ?>
                <input type="hidden" name="filters[]" value="<?= esc($activeFilter) ?>">
            <?php endforeach; ?>
            <input type="search" name="q" value="<?= esc($search ?? '') ?>" placeholder="Search coir products..." aria-label="Search products">
            <select name="sort" aria-label="Sort products">
                <option value="">Sort By</option>
                <option value="price_asc" <?= ($sort ?? '') === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= ($sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="alpha_asc" <?= ($sort ?? '') === 'alpha_asc' ? 'selected' : '' ?>>Alphabetical: A-Z</option>
                <option value="alpha_desc" <?= ($sort ?? '') === 'alpha_desc' ? 'selected' : '' ?>>Alphabetical: Z-A</option>
                <option value="release_desc" <?= ($sort ?? '') === 'release_desc' ? 'selected' : '' ?>>Release Date: Newest</option>
            </select>
            <button type="submit">Apply</button>
        </form>

        <div class="grid products storefront-products-grid">
            <?php foreach ($products as $p): ?>
                <article class="product-card reveal-on-scroll">
                    <img src="<?= esc($p['image_url']) ?>" alt="<?= esc($p['name']) ?>">
                    <h3 class="product-title"><?= esc($p['name']) ?></h3>
                    <p class="muted"><?= esc($p['description']) ?></p>
                    <div class="product-meta">
                        <span class="price">PHP <?= number_format((float) $p['price'], 2) ?></span>
                        <span class="badge">Stock: <?= (int) $p['stock'] ?> | Sold: <?= (int) ($p['sold_count'] ?? 0) ?></span>
                    </div>
                    <div class="review-snippet">
                        <?php if (! empty($p['latest_review'])): ?>
                            <strong>Latest Review by <?= esc($p['latest_review']['full_name'] ?? 'Verified Buyer') ?>:</strong>
                            <div class="star-line"><?= str_repeat('★', (int) $p['latest_review']['rating']) . str_repeat('☆', 5 - (int) $p['latest_review']['rating']) ?></div>
                            <div class="muted"><?= esc($p['latest_review']['comment'] ?: 'No comment provided.') ?></div>
                        <?php else: ?>
                            <strong>Latest Review:</strong>
                            <div class="muted">No product reviews</div>
                        <?php endif; ?>
                    </div>
                    <form method="post" action="<?= base_url('/cart/add/' . $p['id']) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="redirect_to" value="<?= current_url() ?>">
                        <div class="qty-stepper">
                            <button type="button" class="qty-step-btn" data-qty-step="-1">−</button>
                            <input type="number" min="1" max="<?= (int) $p['stock'] ?>" name="quantity" value="1" class="qty-step-input">
                            <button type="button" class="qty-step-btn" data-qty-step="+1">+</button>
                        </div>
                        <button type="submit">Add to Cart</button>
                    </form>
                    <a class="btn secondary" href="<?= base_url('/products/' . $p['id']) ?>">View Product</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
