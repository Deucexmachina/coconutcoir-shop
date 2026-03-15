<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel" style="max-width:520px; margin:0 auto;">
    <h1><?= esc($title) ?></h1>
    <form method="post" action="<?= base_url('/login') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="role" value="<?= esc($role) ?>">
        <label>Email Address
            <input type="email" name="email" required value="<?= old('email') ?>">
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Log In</button>
    </form>
</section>

<?= $this->endSection() ?>
