<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1>Checkout</h1>
    <table>
        <thead><tr><th>Product</th><th>Qty</th><th>Line Total</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr><td><?= esc($item['name']) ?></td><td><?= (int) $item['quantity'] ?></td><td>PHP <?= number_format((float) $item['price'] * (int) $item['quantity'], 2) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Total: PHP <?= number_format((float) $subtotal, 2) ?></h3>

    <form method="post" action="<?= base_url('/checkout') ?>">
        <?= csrf_field() ?>
        <label>Payment Method
            <select name="payment_method" required>
                <option value="">Select</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="GCash">GCash</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
        </label>
        <label>Receive Product By
            <select name="delivery_method" required>
                <option value="">Select</option>
                <option value="Pickup">Pickup</option>
                <option value="Delivery">Delivery</option>
            </select>
        </label>
        <button type="submit">Place Order</button>
    </form>
</section>

<?= $this->endSection() ?>
