<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1 class="section-title">Your Cart</h1>
    <?php if ($items === []): ?>
        <p>Your cart is empty. <a href="<?= base_url('/products') ?>">Browse products</a>.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= esc($item['name']) ?></td>
                    <td>PHP <?= number_format((float) $item['price'], 2) ?></td>
                    <td>
                        <form method="post" action="<?= base_url('/cart/update/' . $item['id']) ?>" class="cart-qty-form" style="display:flex; gap:0.4rem;">
                            <?= csrf_field() ?>
                            <input type="number" name="quantity" min="1" max="<?= (int) $item['stock'] ?>" value="<?= (int) $item['quantity'] ?>" data-auto-submit="change">
                        </form>
                    </td>
                    <td>PHP <?= number_format((float) $item['price'] * (int) $item['quantity'], 2) ?></td>
                    <td>
                        <form method="post" action="<?= base_url('/cart/remove/' . $item['id']) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h3 style="text-align:right;" class="section-title">Subtotal: PHP <?= number_format((float) $subtotal, 2) ?></h3>
        <a class="btn" href="<?= base_url('/checkout') ?>">Proceed to Checkout</a>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
