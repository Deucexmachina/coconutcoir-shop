<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1 class="section-title">Your Cart</h1>
    <?php if ($items === []): ?>
        <p>Your cart is empty. <a href="<?= base_url('/products') ?>">Browse products</a>.</p>
    <?php else: ?>
        <div class="checkout-layout cart-page-layout">
            <div class="panel item-panel cart-items-panel">
                <?php foreach ($items as $item): ?>
                    <article class="cart-item-card">
                        <img src="<?= esc($item['image_url']) ?>" alt="<?= esc($item['name']) ?>">
                        <div class="cart-item-main">
                            <h3 class="cart-item-name"><?= esc($item['name']) ?></h3>
                            <div class="cart-item-price">PHP <?= number_format((float) $item['price'], 2) ?></div>
                            <div class="muted">In stock: <?= (int) $item['stock'] ?></div>
                        </div>
                        <div class="cart-item-actions">
                            <form method="post" action="<?= base_url('/cart/update/' . $item['id']) ?>" class="cart-qty-form">
                                <?= csrf_field() ?>
                                <div class="qty-stepper">
                                    <button type="button" class="qty-step-btn" data-qty-step="-1">−</button>
                                    <input type="number" name="quantity" min="1" max="<?= (int) $item['stock'] ?>" value="<?= (int) $item['quantity'] ?>" data-auto-submit="change" class="qty-step-input">
                                    <button type="button" class="qty-step-btn" data-qty-step="+1">+</button>
                                </div>
                            </form>
                            <div class="cart-line-total">PHP <?= number_format((float) $item['price'] * (int) $item['quantity'], 2) ?></div>
                            <form method="post" action="<?= base_url('/cart/remove/' . $item['id']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn danger">Remove</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <form method="post" action="<?= base_url('/checkout') ?>" class="panel summary-panel cart-order-summary">
                <?= csrf_field() ?>
                <h2 class="section-title">Order Summary</h2>
                <?php if (! $canCheckout): ?>
                    <p class="muted">Login is required to place your order. Your cart stays saved.</p>
                    <a class="btn" href="<?= base_url('/login?redirect_to=' . rawurlencode((string) base_url('/cart'))) ?>">Login to Checkout</a>
                <?php else: ?>
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
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>
</section>

<script>
    window.checkoutVouchers = <?= json_encode($vouchers ?? []) ?>;
    window.checkoutSubtotal = <?= json_encode((float) $subtotal) ?>;
    window.checkoutShippingEstimate = <?= json_encode((float) $shippingEstimate) ?>;
</script>

<?= $this->endSection() ?>
