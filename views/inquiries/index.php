<h1 class="h3 mb-3">Customer Inquiries</h1>

<?php foreach ($inquiries as $inq): ?>
    <?php
    if ($inq['status'] == 'Pending') {
        $badge = 'warning';
    } elseif ($inq['status'] == 'Approved') {
        $badge = 'success';
    } else {
        $badge = 'danger';
    }
    $total = 0;
    ?>
    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between">
            <span>
                <strong>#<?= $inq['inquiry_id'] ?> - <?= e($inq['customer_name']) ?></strong>
                <span class="text-muted">(<?= e($inq['inquiry_type']) ?>)</span>
            </span>
            <span class="badge bg-<?= $badge ?><?= $badge == 'warning' ? ' text-dark' : '' ?>"><?= e($inq['status']) ?></span>
        </div>
        <div class="card-body">
            <p class="small text-muted mb-3">
                Phone: <?= e($inq['customer_phone']) ?>
                <?php if ($inq['customer_email']): ?> | Email: <?= e($inq['customer_email']) ?><?php endif; ?>
                <?php if ($inq['customer_address']): ?> | Address: <?= e($inq['customer_address']) ?><?php endif; ?><br>
                Sent: <?= e($inq['date_sent']) ?>
                <?php if ($inq['message']): ?> | Message: <?= e($inq['message']) ?><?php endif; ?>
                <?php if ($inq['handled_by_name']): ?> | Handled by: <?= e($inq['handled_by_name']) ?><?php endif; ?>
            </p>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Qty</th><th>In stock</th><th class="text-end">Unit price</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($inq['items'] as $it): ?>
                        <?php $sub = $it['quantity'] * $it['unit_price']; $total = $total + $sub; ?>
                        <tr class="<?= $it['stock'] < $it['quantity'] ? 'table-warning' : '' ?>">
                            <td><?= e($it['product_name']) ?></td>
                            <td><?= $it['quantity'] ?></td>
                            <td><?= $it['stock'] ?></td>
                            <td class="text-end"><?= number_format($it['unit_price'], 2) ?></td>
                            <td class="text-end"><?= number_format($sub, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="4" class="text-end">Total</th><th class="text-end"><?= number_format($total, 2) ?></th></tr>
                    </tfoot>
                </table>
            </div>
            <?php if ($inq['status'] == 'Pending'): ?>
                <form method="post" action="<?= url('inquiry_decide') ?>" class="d-flex gap-2">
<?= csrf_field() ?>
                    <input type="hidden" name="inquiry_id" value="<?= $inq['inquiry_id'] ?>">
                    <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                    <button name="action" value="reject" class="btn btn-outline-danger btn-sm">Reject</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
<?php if (!$inquiries): ?><div class="alert alert-secondary">No inquiries yet.</div><?php endif; ?>
