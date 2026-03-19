<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1 class="section-title">My Profile</h1>
    <div class="profile-grid">
        <article class="panel profile-card">
            <h2>My Profile</h2>
            <table>
                <tr><th>Name</th><td><?= esc($user['full_name']) ?></td></tr>
                <tr><th>Email</th><td><?= esc($user['email']) ?></td></tr>
                <tr><th>Address</th><td><?= esc($user['address']) ?></td></tr>
                <tr><th>Mobile Number</th><td><?= esc($user['mobile_number']) ?></td></tr>
                <tr><th>Role</th><td><?= esc($user['role']) ?></td></tr>
            </table>
        </article>

        <article class="panel profile-orders-card" id="my-orders">
            <h2>My Orders</h2>
            <?php if ($orders === []): ?>
                <p>No orders yet.</p>
            <?php endif; ?>
            <?php foreach ($orders as $order): ?>
                <article class="panel transaction-card">
                    <div class="transaction-head">
                        <h3>Order #<?= (int) $order['id'] ?></h3>
                        <span class="badge"><?= esc($order['status']) ?></span>
                    </div>
                    <div class="muted">
                        <?= esc($order['created_at']) ?> | <?= esc($order['payment_method']) ?> | <?= esc($order['delivery_method']) ?>
                    </div>
                    <div class="summary-line"><span>Items Subtotal</span><strong>PHP <?= number_format((float) ($order['subtotal_amount'] ?? 0), 2) ?></strong></div>
                    <div class="summary-line"><span>Shipping Fee</span><strong>PHP <?= number_format((float) ($order['shipping_fee'] ?? 0), 2) ?></strong></div>
                    <div class="summary-line"><span>Voucher</span><strong><?= esc($order['voucher_type'] ?? 'None') ?></strong></div>
                    <div class="summary-line"><span>Discount</span><strong>- PHP <?= number_format((float) ($order['voucher_discount_amount'] ?? 0), 2) ?></strong></div>
                    <div class="summary-line total"><span>Total</span><strong>PHP <?= number_format((float) $order['total_amount'], 2) ?></strong></div>
                    <div class="muted">Address: <?= esc($order['shipping_address'] ?? 'N/A') ?></div>

                    <?php foreach (($orderItemsByOrder[$order['id']] ?? []) as $item): ?>
                        <article class="cart-item-card">
                            <img src="<?= esc($item['image_url']) ?>" alt="<?= esc($item['name']) ?>">
                            <div>
                                <h4><?= esc($item['name']) ?></h4>
                                <div class="muted">Qty: <?= (int) $item['quantity'] ?> | PHP <?= number_format((float) $item['price_each'], 2) ?> each</div>
                            </div>
                            <div class="cart-item-actions">
                                <strong>PHP <?= number_format((float) $item['line_total'], 2) ?></strong>
                            </div>
                        </article>
                        <form method="post" action="<?= base_url('/reviews/submit/' . (int) $item['product_id']) ?>" class="review-form">
                            <?= csrf_field() ?>
                            <label>Rate this product</label>
                            <?php $existing = $reviewsByProduct[(int) $item['product_id']] ?? null; ?>
                            <div class="star-pick" data-star-pick>
                                <input type="hidden" name="rating" value="<?= (int) ($existing['rating'] ?? 0) ?>" required>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" data-star-value="<?= $i ?>" class="<?= (int) ($existing['rating'] ?? 0) >= $i ? 'is-active' : '' ?>">★</button>
                                <?php endfor; ?>
                            </div>
                            <textarea name="comment" rows="2" placeholder="Optional comment"><?= esc($existing['comment'] ?? '') ?></textarea>
                            <button type="submit"><?= $existing ? 'Update Review' : 'Submit Review' ?></button>
                        </form>
                    <?php endforeach; ?>
                </article>
            <?php endforeach; ?>
        </article>
    </div>
</section>

<?= $this->endSection() ?>
