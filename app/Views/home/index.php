<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="announcement-strip">
    <div class="announcement-marquee">
        <div class="announcement-track">
            <?php $announcement = $setting['announcement'] ?? 'Now shipping nationwide with eco-friendly coconut coir products.'; ?>
            <?php for ($i = 0; $i < 6; $i++): ?>
                <span class="announcement-item">
                    <svg viewBox="0 0 24 24" width="15" height="15"><path fill="currentColor" d="M12 2l2.4 4.9L20 8l-4 3.8l1 5.6L12 15l-5 2.4l1-5.6L4 8l5.6-1.1z"/></svg>
                    <?= esc($announcement) ?>
                </span>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="home-hero" style="background-image:linear-gradient(90deg, rgba(10,55,40,.84) 0%, rgba(10,55,40,.65) 40%, rgba(10,55,40,.18) 64%, rgba(10,55,40,0) 78%), url('<?= esc($setting['hero_background_image'] ?? 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&w=1400&q=80') ?>');">
    <div class="home-hero-content">
        <h1><?= esc($setting['title'] ?? $setting['hero_title'] ?? 'CoirCraft Coconut Coir Products') ?></h1>
        <p><?= esc($setting['description'] ?? $setting['hero_subtitle'] ?? 'Eco-friendly coconut coir products for every need.') ?></p>
    </div>
</section>

<section class="featured-loop-strip">
    <div class="featured-loop-track">
        <?php for ($i = 0; $i < 6; $i++): ?>
            <span>Check our featured products here!</span>
        <?php endfor; ?>
    </div>
</section>

<section class="home-featured-section">
    <div class="featured-grid three-col-grid">
        <?php foreach ($featured as $item): ?>
            <article class="featured-product-tile reveal-on-scroll">
                <img src="<?= esc($item['image_url']) ?>" alt="<?= esc($item['name']) ?>">
                <div class="featured-overlay">
                    <h3><?= esc($item['name']) ?></h3>
                    <p>PHP <?= number_format((float) $item['price'], 2) ?></p>
                </div>
                <form method="post" action="<?= base_url('/cart/add/' . $item['id']) ?>" class="featured-quick-add">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect_to" value="<?= current_url() ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" aria-label="Quick add to cart">
                        <svg viewBox="0 0 24 24" width="17" height="17"><path fill="currentColor" d="M7 4h-2l-1 2v2h2l2.6 5.6L7.3 16c-.2.3-.3.6-.3 1a2 2 0 0 0 2 2h10v-2H9.4l.9-1.5h7.4a2 2 0 0 0 1.8-1.1L22 8H8.1L7 6h14V4H7z"/></svg>
                    </button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?= $this->endSection() ?>
