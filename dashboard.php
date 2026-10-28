<?php

include 'config.php';
require_role(['Owner', 'Sales Staff', 'Inventory Staff']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['read_alert'])) {
    run('UPDATE low_stock_alerts SET is_read = 1 WHERE alert_id = ?', [$_POST['read_alert']]);
    redirect('dashboard.php');
}

$alerts = get_rows('SELECT * FROM low_stock_alerts WHERE is_read = 0 ORDER BY created_at DESC');
$low = get_rows('SELECT * FROM low_stock_products ORDER BY stock');
$pendingCount = get_row("SELECT COUNT(*) AS total FROM inquiries WHERE status = 'Pending'");
$pendingList = get_rows(
    "SELECT inquiry_id, customer_name, customer_phone, inquiry_type, date_sent
     FROM inquiries WHERE status = 'Pending' ORDER BY date_sent DESC LIMIT 5"
);
$today = get_row('SELECT * FROM daily_sales WHERE sale_day = CURDATE()');

$staffPage = true;
$pageTitle = 'Dashboard';
include 'header.php';
?>

<h1 class="h3 mb-3">Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm text-center"><div class="card-body">
            <div class="text-muted">Pending inquiries</div>
            <div class="display-6"><?= (int) $pendingCount['total'] ?></div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center"><div class="card-body">
            <div class="text-muted">Sales today</div>
            <div class="display-6"><?= $today ? (int) $today['total_sales'] : 0 ?></div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm text-center"><div class="card-body">
            <div class="text-muted">Income today</div>
            <div class="display-6"><?= number_format($today['total_income'] ?? 0, 2) ?></div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between">
        <span>Pending Inquiries</span>
        <a href="inquiries.php">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Name</th><th>Phone</th><th>Type</th><th>Sent</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($pendingList as $i): ?>
                <tr>
                    <td><?= $i['inquiry_id'] ?></td>
                    <td><?= e($i['customer_name']) ?></td>
                    <td><?= e($i['customer_phone']) ?></td>
                    <td><?= e($i['inquiry_type']) ?></td>
                    <td><?= e($i['date_sent']) ?></td>
                    <td><a href="inquiries.php" class="btn btn-sm btn-outline-primary">Review</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$pendingList): ?>
                <tr><td colspan="6" class="text-center text-muted">No pending inquiries.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">Unread Low-Stock Alerts</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <tbody>
            <?php foreach ($alerts as $a): ?>
                <tr>
                    <td><?= e($a['message']) ?></td>
                    <td class="text-muted"><?= e($a['created_at']) ?></td>
                    <td class="text-end">
                        <form method="post">
                            <input type="hidden" name="read_alert" value="<?= $a['alert_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Mark read</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$alerts): ?>
                <tr><td class="text-center text-muted">No alerts.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">Low-Stock Products</div>
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead class="table-light">
                <tr><th>Product</th><th>Category</th><th>Stock</th><th>Threshold</th></tr>
            </thead>
            <tbody>
            <?php foreach ($low as $p): ?>
                <tr>
                    <td><?= e($p['product_name']) ?></td>
                    <td><?= e($p['category_name']) ?></td>
                    <td><span class="badge bg-danger"><?= $p['stock'] ?></span></td>
                    <td><?= $p['low_stock_level'] ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$low): ?>
                <tr><td colspan="4" class="text-center text-muted">All stocked.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
