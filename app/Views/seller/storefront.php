<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1 class="section-title">Seller Storefront Management</h1>
    <form method="post" action="<?= base_url('/seller/storefront') ?>">
        <?= csrf_field() ?>
        <div class="grid" style="grid-template-columns:1fr 1fr;">
            <label>Title<input type="text" name="title" value="<?= esc($setting['title'] ?? $setting['hero_title'] ?? '') ?>" required style="height:45px;"></label>
            <label>Description<input type="text" name="description" value="<?= esc($setting['description'] ?? $setting['hero_subtitle'] ?? '') ?>" required style="height:45px;"></label>
        </div>
        <label>Hero Background Image URL
            <input type="url" name="hero_background_image" value="<?= esc($setting['hero_background_image'] ?? '') ?>" placeholder="https://..." style="height:45px;">
        </label>
        <label>Announcement
            <textarea name="announcement" rows="8" style="min-height:160px;"><?= esc($setting['announcement'] ?? '') ?></textarea>
        </label>
        <button type="submit">Save Storefront</button>
    </form>
</section>

<?= $this->endSection() ?>
