<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <article class="product-card product-detail-card">
        <img src="<?= esc($product['image_url']) ?>" alt="<?= esc($product['name']) ?>">
        <h1 class="section-title"><?= esc($product['name']) ?></h1>
        <p><?= esc($product['description']) ?></p>
        <?php if (! empty($product['additional_details'])): ?>
            <section class="product-detail-specs">
                <h3>Details</h3>
                <ul>
                    <?php foreach (preg_split('/\r\n|\r|\n/', (string) $product['additional_details']) as $detail): ?>
                        <?php $detail = trim($detail); ?>
                        <?php if ($detail !== ''): ?>
                            <li><?= esc($detail) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
        <div class="product-meta">
            <span class="price">PHP <?= number_format((float) $product['price'], 2) ?></span>
            <span class="badge">Stock: <?= (int) $product['stock'] ?> | Sold: <?= (int) ($product['sold_count'] ?? 0) ?></span>
        </div>
        <div class="review-snippet">
            <?php if (! empty($product['latest_review'])): ?>
                <strong>Latest Review by <?= esc($product['latest_review']['full_name'] ?? 'Verified Buyer') ?>:</strong>
                <div class="star-line"><?= str_repeat('★', (int) $product['latest_review']['rating']) . str_repeat('☆', 5 - (int) $product['latest_review']['rating']) ?></div>
                <div class="muted"><?= esc($product['latest_review']['comment'] ?: 'No comment provided.') ?></div>
            <?php else: ?>
                <strong>Latest Review:</strong>
                <div class="muted">No product reviews</div>
            <?php endif; ?>
        </div>
        <form method="post" action="<?= base_url('/cart/add/' . $product['id']) ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="redirect_to" value="<?= current_url() ?>">
            <div class="qty-stepper">
                <button type="button" class="qty-step-btn" data-qty-step="-1">−</button>
                <input type="number" min="1" max="<?= (int) $product['stock'] ?>" name="quantity" value="1" class="qty-step-input">
                <button type="button" class="qty-step-btn" data-qty-step="+1">+</button>
            </div>
            <button type="submit">Add to Cart</button>
        </form>
    </article>
</section>

<?= $this->endSection() ?>
