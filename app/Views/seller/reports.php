<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1>Seller Reports</h1>
    <div class="grid" style="grid-template-columns:1fr 1fr;">
        <article class="panel">
            <h3>Daily Sales</h3>
            <p class="price">PHP <?= number_format((float) $dailyTotal, 2) ?></p>
        </article>
        <article class="panel">
            <h3>Monthly Sales</h3>
            <p class="price">PHP <?= number_format((float) $monthlyTotal, 2) ?></p>
        </article>
    </div>
</section>

<section class="panel">
    <h2>Basic Inventory Report</h2>
    <table>
        <thead><tr><th>Product</th><th>Stock</th><th>Price</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($inventoryRows as $row): ?>
            <tr>
                <td><?= esc($row['name']) ?></td>
                <td><?= (int) $row['stock'] ?></td>
                <td>PHP <?= number_format((float) $row['price'], 2) ?></td>
                <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>
