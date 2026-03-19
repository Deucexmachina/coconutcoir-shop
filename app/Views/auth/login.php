<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel" style="max-width:520px; margin:0 auto;">
    <h1><?= esc($title) ?></h1>
    <form method="post" action="<?= base_url('/login') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="role" value="<?= esc($role) ?>">
        <input type="hidden" name="redirect_to" value="<?= esc($redirect_to ?? '') ?>">
        <label>Email Address
            <input type="email" name="email" required value="<?= old('email') ?>">
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Log In</button>
    </form>
    <div class="auth-links-row">
        <?php if (($role ?? 'buyer') === 'buyer'): ?>
            <a class="btn auth-link-register" href="<?= base_url('/register') ?>">Register Account</a>
            <a class="btn auth-link-seller" href="<?= base_url('/seller/login') ?>">Seller Login</a>
        <?php else: ?>
            <a class="btn auth-link-buyer" href="<?= base_url('/login') ?>">Buyer Login</a>
            <a class="btn auth-link-register" href="<?= base_url('/register') ?>">Register Account</a>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
