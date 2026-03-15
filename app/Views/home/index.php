<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="hero">
    <h1><?= esc($setting['hero_title'] ?? 'CoirCraft Coconut Coir Products') ?></h1>
    <p><?= esc($setting['hero_subtitle'] ?? 'Eco-friendly coconut coir products for every need.') ?></p>
    <p class="muted"><?= esc($setting['announcement'] ?? 'Shop now and support sustainability.') ?></p>
    <div style="margin-top:0.8rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
        <a class="btn" href="<?= base_url('/storefront') ?>">Go to Storefront</a>
        <a class="btn secondary" href="<?= base_url('/register') ?>">Create Buyer Account</a>
    </div>
</section>

<section class="panel">
    <h2 class="section-title featured">Featured Products</h2>
    <div class="grid products">
        <?php foreach ($featured as $item): ?>
            <article class="product-card">
                <img src="<?= esc($item['image_url']) ?>" alt="<?= esc($item['name']) ?>">
                <h3 class="product-title"><?= esc($item['name']) ?></h3>
                <p class="muted"><?= esc($item['description'] ?? 'Premium quality coconut coir product') ?></p>
                <div class="product-meta">
                    <span class="price">PHP <?= number_format((float) $item['price'], 2) ?></span>
                    <span class="badge">Stock: <?= (int) ($item['stock'] ?? 0) ?></span>
                </div>
                <form method="post" action="<?= base_url('/cart/add/' . $item['id']) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect_to" value="<?= current_url() ?>">
                    <div class="quantity-control">
                        <input type="number" min="1" max="<?= (int) ($item['stock'] ?? 1) ?>" name="quantity" value="1" class="quantity-input">
                        <div class="quantity-buttons">
                            <button type="button" class="quantity-btn" onclick="this.parentElement.previousElementSibling.value = Math.max(1, parseInt(this.parentElement.previousElementSibling.value) - 1)">−</button>
                            <button type="button" class="quantity-btn" onclick="this.parentElement.previousElementSibling.value = Math.min(<?= (int) ($item['stock'] ?? 1) ?>, parseInt(this.parentElement.previousElementSibling.value) + 1)">+</button>
                        </div>
                    </div>
                    <button type="submit">Add to Cart</button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?= $this->endSection() ?>
