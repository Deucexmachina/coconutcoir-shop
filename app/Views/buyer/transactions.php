<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<section class="panel">
    <h1>Transaction History</h1>
    <table>
        <thead><tr><th>ID</th><th>Date</th><th>Total</th><th>Payment</th><th>Delivery</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= (int) $order['id'] ?></td>
                    <td><?= esc($order['created_at']) ?></td>
                    <td>PHP <?= number_format((float) $order['total_amount'], 2) ?></td>
                    <td><?= esc($order['payment_method']) ?></td>
                    <td><?= esc($order['delivery_method']) ?></td>
                    <td><?= esc($order['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?= $this->endSection() ?>
