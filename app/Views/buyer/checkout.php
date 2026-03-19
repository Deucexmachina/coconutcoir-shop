<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1 class="section-title">Checkout</h1>
    <form method="post" action="<?= base_url('/checkout') ?>">
        <?= csrf_field() ?>
        <div class="checkout-layout">
            <div class="panel item-panel">
                <h2>Items</h2>
                <?php foreach ($items as $item): ?>
                    <article class="cart-item-card">
                        <img src="<?= esc($item['image_url']) ?>" alt="<?= esc($item['name']) ?>">
                        <div>
                            <h3><?= esc($item['name']) ?></h3>
                            <div class="muted">PHP <?= number_format((float) $item['price'], 2) ?> each</div>
                            <div class="muted">Qty: <?= (int) $item['quantity'] ?></div>
                        </div>
                        <div class="cart-item-actions">
                            <strong>PHP <?= number_format((float) $item['price'] * (int) $item['quantity'], 2) ?></strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <aside class="panel summary-panel">
                <h2>Order Summary</h2>
                <label>Shipping Address
                    <select id="savedAddressSelect">
                        <option value="">Type a new address</option>
                        <?php foreach ($addresses as $address): ?>
                            <option value="<?= esc($address) ?>"><?= esc($address) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Selected Address
                    <textarea id="shippingAddressInput" name="shipping_address" rows="3" required><?= esc($defaultAddress) ?></textarea>
                </label>
                <label>Voucher Code
                    <input type="text" name="voucher_code" id="voucherCodeInput" placeholder="Enter Voucher Code">
                </label>
                <div class="muted" id="voucherDetails">No voucher applied</div>
                <label>Payment Method
                    <select name="payment_method" required>
                        <option value="">Select</option>
                        <option value="Cash on Delivery">Cash on Delivery</option>
                        <option value="Credit/Debit Card">Credit/Debit Card</option>
                        <option value="E-Wallet">E-Wallet</option>
                    </select>
                </label>
                <label>Receive Product By
                    <select name="delivery_method" id="deliveryMethodInput" required>
                        <option value="Delivery">Delivery</option>
                        <option value="Pickup">Pickup</option>
                    </select>
                </label>
                <div class="summary-line"><span>Items Subtotal</span><strong id="subtotalValue">PHP <?= number_format((float) $subtotal, 2) ?></strong></div>
                <div class="summary-line"><span>Shipping Fee</span><strong id="shippingFeeValue">PHP <?= number_format((float) $shippingEstimate, 2) ?></strong></div>
                <div class="summary-line"><span>Voucher Discount</span><strong id="discountValue">- PHP 0.00</strong></div>
                <div class="summary-line total"><span>Total</span><strong id="totalValue">PHP <?= number_format((float) ($subtotal + $shippingEstimate), 2) ?></strong></div>

                <button type="submit">Place Order</button>
            </aside>
        </div>
    </form>
</section>

<script>
    window.checkoutVouchers = <?= json_encode($vouchers) ?>;
    window.checkoutSubtotal = <?= json_encode((float) $subtotal) ?>;
    window.checkoutShippingEstimate = <?= json_encode((float) $shippingEstimate) ?>;
</script>

<?= $this->endSection() ?>
