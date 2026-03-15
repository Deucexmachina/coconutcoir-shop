<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1 class="section-title">Inventory Management</h1>
    <h3 class="section-title">Add New Product</h3>
    <form method="post" action="<?= base_url('/seller/inventory/create') ?>">
        <?= csrf_field() ?>
        <div class="grid" style="grid-template-columns:1fr 1fr;">
            <label>Name<input type="text" name="name" required></label>
            <label>Price<input type="number" step="0.01" name="price" required></label>
            <label>Stock<input type="number" name="stock" required></label>
            <label>Image URL<input type="url" name="image_url" required data-image-preview-input="#createProductPreview"></label>
        </div>
        <div class="image-preview-box">
            <img src="https://placehold.co/440x280/e8efff/264d8f?text=Image+Preview" id="createProductPreview" alt="New product image preview">
        </div>
        <label>Description<textarea name="description" required></textarea></label>
        <div style="display:flex; gap:0.8rem; flex-wrap:wrap;">
            <label><input type="checkbox" name="is_featured" value="1"> Featured</label>
            <label><input type="checkbox" name="is_trending" value="1"> Trending</label>
            <label><input type="checkbox" name="is_best_seller" value="1"> Best Seller</label>
        </div>
        <button type="submit">Create Product</button>
    </form>
</section>

<section class="panel">
    <h3 class="section-title">Current Products</h3>
    <?php foreach ($products as $p): ?>
        <form method="post" action="<?= base_url('/seller/inventory/update/' . $p['id']) ?>" class="panel" style="margin-bottom:0.8rem;">
            <?= csrf_field() ?>
            <strong>#<?= (int) $p['id'] ?> <?= esc($p['name']) ?></strong>
            <div class="grid" style="grid-template-columns:1fr 1fr; margin-top:0.5rem;">
                <label>Name<input type="text" name="name" value="<?= esc($p['name']) ?>" required></label>
                <label>Price<input type="number" step="0.01" name="price" value="<?= esc($p['price']) ?>" required></label>
                <label>Stock<input type="number" name="stock" value="<?= esc($p['stock']) ?>" required></label>
                <label>Image URL<input type="url" name="image_url" value="<?= esc($p['image_url']) ?>" required data-image-preview-input="#productPreview<?= (int) $p['id'] ?>"></label>
            </div>
            <div class="image-preview-box">
                <img src="<?= esc($p['image_url']) ?>" id="productPreview<?= (int) $p['id'] ?>" alt="Product image preview for <?= esc($p['name']) ?>">
            </div>
            <label>Description<textarea name="description" required><?= esc($p['description']) ?></textarea></label>
            <div style="display:flex; gap:0.8rem; flex-wrap:wrap;">
                <label><input type="checkbox" name="is_featured" value="1" <?= $p['is_featured'] ? 'checked' : '' ?>> Featured</label>
                <label><input type="checkbox" name="is_trending" value="1" <?= $p['is_trending'] ? 'checked' : '' ?>> Trending</label>
                <label><input type="checkbox" name="is_best_seller" value="1" <?= $p['is_best_seller'] ? 'checked' : '' ?>> Best Seller</label>
                <label><input type="checkbox" name="is_active" value="1" <?= $p['is_active'] ? 'checked' : '' ?>> Active</label>
            </div>
            <button type="submit">Update Product</button>
        </form>
    <?php endforeach; ?>
</section>

<?= $this->endSection() ?>
