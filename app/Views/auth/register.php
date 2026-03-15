<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel" style="max-width:620px; margin:0 auto;">
    <h1>Buyer Registration</h1>
    <form method="post" action="<?= base_url('/register') ?>">
        <?= csrf_field() ?>
        <label>Email Address
            <input type="email" name="email" required value="<?= old('email') ?>">
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <label>Confirm Password
            <input type="password" name="confirm_password" required>
        </label>
        <label>Complete Name
            <input type="text" name="full_name" required value="<?= old('full_name') ?>">
        </label>
        <label>Address
            <textarea name="address" required><?= old('address') ?></textarea>
        </label>
        <label>Mobile Number
            <input type="text" name="mobile_number" required value="<?= old('mobile_number') ?>">
        </label>
        <button type="submit">Register</button>
    </form>
</section>

<?= $this->endSection() ?>
