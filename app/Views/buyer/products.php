<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1 class="section-title">All Products</h1>
    <div class="grid products">
        <?php foreach ($products as $p): ?>
            <article class="product-card">
                <img src="<?= esc($p['image_url']) ?>" alt="<?= esc($p['name']) ?>">
                <h3 class="product-title"><?= esc($p['name']) ?></h3>
                <p class="muted"><?= esc($p['description']) ?></p>
                <div class="product-meta">
                    <span class="price">PHP <?= number_format((float) $p['price'], 2) ?></span>
                    <span class="badge">Stock: <?= (int) $p['stock'] ?></span>
                </div>
                <form method="post" action="<?= base_url('/cart/add/' . $p['id']) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect_to" value="<?= current_url() ?>">
                    <div class="quantity-control">
                        <input type="number" min="1" max="<?= (int) $p['stock'] ?>" name="quantity" value="1" class="quantity-input">
                        <div class="quantity-buttons">
                            <button type="button" class="quantity-btn" onclick="this.parentElement.previousElementSibling.value = Math.max(1, parseInt(this.parentElement.previousElementSibling.value) - 1)">−</button>
                            <button type="button" class="quantity-btn" onclick="this.parentElement.previousElementSibling.value = Math.min(<?= (int) $p['stock'] ?>, parseInt(this.parentElement.previousElementSibling.value) + 1)">+</button>
                        </div>
                    </div>
                    <button type="submit">Add to Cart</button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?= $this->endSection() ?>
