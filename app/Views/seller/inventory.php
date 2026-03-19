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
            <label>Release Date<input type="date" name="release_date"></label>
        </div>
        <div class="image-preview-box">
            <img src="https://placehold.co/440x280/e8efff/264d8f?text=Image+Preview" id="createProductPreview" alt="New product image preview">
        </div>
        <label>Description<textarea name="description" required></textarea></label>
        <label>Additional Details (one detail per line)<textarea name="additional_details" placeholder="Material Blend: Coconut fiber&#10;Color: Natural brown&#10;Use: Indoor and outdoor"></textarea></label>
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
    <?php if ($products !== []): ?>
        <form method="get" action="<?= base_url('/seller/inventory') ?>" class="panel" style="margin-bottom:0.8rem;">
            <label>Select Product to Edit
                <select name="product_id" onchange="this.form.submit()">
                    <?php foreach ($products as $row): ?>
                        <option value="<?= (int) $row['id'] ?>" <?= ((int) ($selectedProductId ?? 0) === (int) $row['id']) ? 'selected' : '' ?>>
                            #<?= (int) $row['id'] ?> - <?= esc($row['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </form>

        <?php if (! empty($selectedProduct)): ?>
        <form method="post" action="<?= base_url('/seller/inventory/update/' . $selectedProduct['id']) ?>" class="panel" style="margin-bottom:0.8rem;">
            <?= csrf_field() ?>
            <strong>#<?= (int) $selectedProduct['id'] ?> <?= esc($selectedProduct['name']) ?></strong>
            <div class="grid" style="grid-template-columns:1fr 1fr; margin-top:0.5rem;">
                <label>Name<input type="text" name="name" value="<?= esc($selectedProduct['name']) ?>" required></label>
                <label>Price<input type="number" step="0.01" name="price" value="<?= esc($selectedProduct['price']) ?>" required></label>
                <label>Stock<input type="number" name="stock" value="<?= esc($selectedProduct['stock']) ?>" required></label>
                <label>Image URL<input type="url" name="image_url" value="<?= esc($selectedProduct['image_url']) ?>" required data-image-preview-input="#productPreview<?= (int) $selectedProduct['id'] ?>"></label>
                <label>Release Date<input type="date" name="release_date" value="<?= esc($selectedProduct['release_date'] ?? '') ?>"></label>
            </div>
            <div class="image-preview-box">
                <img src="<?= esc($selectedProduct['image_url']) ?>" id="productPreview<?= (int) $selectedProduct['id'] ?>" alt="Product image preview for <?= esc($selectedProduct['name']) ?>">
            </div>
            <label>Description<textarea name="description" required><?= esc($selectedProduct['description']) ?></textarea></label>
            <label>Additional Details<textarea name="additional_details"><?= esc($selectedProduct['additional_details'] ?? '') ?></textarea></label>
            <div style="display:flex; gap:0.8rem; flex-wrap:wrap;">
                <label><input type="checkbox" name="is_featured" value="1" <?= $selectedProduct['is_featured'] ? 'checked' : '' ?>> Featured</label>
                <label><input type="checkbox" name="is_trending" value="1" <?= $selectedProduct['is_trending'] ? 'checked' : '' ?>> Trending</label>
                <label><input type="checkbox" name="is_best_seller" value="1" <?= $selectedProduct['is_best_seller'] ? 'checked' : '' ?>> Best Seller</label>
                <label><input type="checkbox" name="is_active" value="1" <?= $selectedProduct['is_active'] ? 'checked' : '' ?>> Active</label>
            </div>
            <button type="submit">Update Product</button>
        </form>
        <?php endif; ?>
    <?php else: ?>
        <p class="muted">No products yet. Add your first product above.</p>
    <?php endif; ?>
</section>

<?= $this->endSection() ?>
