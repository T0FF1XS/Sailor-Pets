<?php

include 'config.php';
require_role(['Owner', 'Sales Staff']);

$sale = null;
$items = [];

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $sale = get_row(
        'SELECT s.*, CONCAT(u.first_name, " ", u.last_name) AS cashier
         FROM sales s JOIN users u ON s.cashier_id = u.user_id
         WHERE s.sale_id = ?',
        [$id]
    );
    $items = get_rows(
        'SELECT p.product_name, si.quantity, si.unit_price, si.subtotal
         FROM sale_items si JOIN products p ON si.product_id = p.product_id
         WHERE si.sale_id = ?',
        [$id]
    );
}

$daily = get_rows('SELECT * FROM daily_sales ORDER BY sale_day DESC LIMIT 14');
$recent = get_rows('SELECT * FROM sales ORDER BY sale_id DESC LIMIT 30');

$staffPage = true;
$pageTitle = 'Sales';
include 'header.php';
?>

<h1 class="h3 mb-3 d-print-none">Sales</h1>

<?php if ($sale): ?>
    <div class="card shadow-sm mb-4">
        <div class="card-header">Receipt #<?= $sale['sale_id'] ?></div>
        <div class="card-body">
            <p class="mb-3">
                Date: <?= e($sale['sale_date']) ?><br>
                Cashier: <?= e($sale['cashier']) ?><br>
                Customer: <?= e($sale['customer_name']) ?><br>
                <?= e($sale['price_type']) ?> | <?= e($sale['payment_method']) ?>
            </p>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Qty</th><th class="text-end">Unit price</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td><?= e($it['product_name']) ?></td>
                            <td><?= $it['quantity'] ?></td>
                            <td class="text-end"><?= number_format($it['unit_price'], 2) ?></td>
                            <td class="text-end"><?= number_format($it['subtotal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="3" class="text-end">Total</th><th class="text-end"><?= number_format($sale['total_amount'], 2) ?></th></tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" class="btn btn-outline-secondary d-print-none" onclick="window.print()">Print</button>
        </div>
    </div>
<?php endif; ?>

<div class="d-print-none">
    <div class="card shadow-sm mb-4">
        <div class="card-header">Daily Totals</div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light"><tr><th>Date</th><th>Transactions</th><th class="text-end">Income</th></tr></thead>
                <tbody>
                <?php foreach ($daily as $d): ?>
                    <tr>
                        <td><?= e($d['sale_day']) ?></td>
                        <td><?= $d['total_sales'] ?></td>
                        <td class="text-end"><?= number_format($d['total_income'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$daily): ?><tr><td colspan="3" class="text-center text-muted">No sales yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Recent Sales</div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Date</th><th>Customer</th><th>Price</th><th>Payment</th><th class="text-end">Total</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($recent as $s): ?>
                    <tr>
                        <td><?= $s['sale_id'] ?></td>
                        <td><?= e($s['sale_date']) ?></td>
                        <td><?= e($s['customer_name']) ?></td>
                        <td><?= e($s['price_type']) ?></td>
                        <td><?= e($s['payment_method']) ?></td>
                        <td class="text-end"><?= number_format($s['total_amount'], 2) ?></td>
                        <td><a href="?id=<?= $s['sale_id'] ?>" class="btn btn-sm btn-outline-primary">Receipt</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
