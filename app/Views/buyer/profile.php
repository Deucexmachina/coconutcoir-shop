<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1 class="section-title">My Profile</h1>
    <table>
        <tr><th>Name</th><td><?= esc($user['full_name']) ?></td></tr>
        <tr><th>Email</th><td><?= esc($user['email']) ?></td></tr>
        <tr><th>Address</th><td><?= esc($user['address']) ?></td></tr>
        <tr><th>Mobile Number</th><td><?= esc($user['mobile_number']) ?></td></tr>
        <tr><th>Role</th><td><?= esc($user['role']) ?></td></tr>
    </table>
</section>

<?= $this->endSection() ?>
