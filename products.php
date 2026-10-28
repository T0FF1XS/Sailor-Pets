<?php

include 'config.php';
$user = require_role(['Owner', 'Inventory Staff']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['toggle'])) {
        run('UPDATE products SET is_active = 1 - is_active WHERE product_id = ?', [$_POST['toggle']]);
        redirect('products.php');
    }

    if (isset($_POST['receive'])) {
        $quantity = (int) $_POST['quantity'];
        $date = $_POST['date_received'] ?: date('Y-m-d');

        if ($quantity > 0) {
            run(
                'INSERT INTO stock_in (product_id, user_id, quantity, date_received) VALUES (?, ?, ?, ?)',
                [$_POST['product_id'], $user['user_id'], $quantity, $date]
            );
            flash('Stock received.');
        } else {
            flash('Quantity must be more than zero.');
        }
        redirect('products.php#stock-in');
    }

    $id = (int) $_POST['product_id'];
    $values = [
        $_POST['category_id'], $_POST['product_name'], $_POST['pet_type'],
        $_POST['retail_price'], $_POST['wholesale_price'],
        $_POST['low_stock_level'], $_POST['tag'], $_POST['image'],
    ];

    try {
        if ($id > 0) {
            $values[] = $id;
            run(
                'UPDATE products SET category_id=?, product_name=?, pet_type=?, retail_price=?,
                 wholesale_price=?, low_stock_level=?, tag=?, image=? WHERE product_id=?',
                $values
            );
            flash('Product updated.');
        } else {
            run(
                'INSERT INTO products (category_id, product_name, pet_type, retail_price,
                 wholesale_price, low_stock_level, tag, image) VALUES (?,?,?,?,?,?,?,?)',
                $values
            );
            flash('Product added. Use Stock In to add stock.');
        }
    } catch (mysqli_sql_exception $e) {
        flash('Could not save. Wholesale price must not be higher than retail price.');
    }
    redirect('products.php');
}

$edit = [];
if (isset($_GET['edit'])) {
    $edit = get_row('SELECT * FROM products WHERE product_id = ?', [(int) $_GET['edit']]) ?: [];
}

$categories = get_rows('SELECT * FROM categories ORDER BY category_name');
$products = get_rows(
    'SELECT p.*, c.category_name FROM products p
     JOIN categories c ON p.category_id = c.category_id
     ORDER BY p.product_id DESC'
);
$activeProducts = get_rows('SELECT product_id, product_name, stock FROM products WHERE is_active = 1 ORDER BY product_name');
$history = get_rows(
    'SELECT s.*, p.product_name, CONCAT(u.first_name, " ", u.last_name) AS received_by
     FROM stock_in s
     JOIN products p ON s.product_id = p.product_id
     JOIN users u ON s.user_id = u.user_id
     ORDER BY s.stock_in_id DESC LIMIT 30'
);

$staffPage = true;
$pageTitle = 'Products';
include 'header.php';
?>

<h1 class="h3 mb-3">Products</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header"><?= $edit ? 'Edit Product' : 'Add Product' ?></div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <input type="hidden" name="product_id" value="<?= e($edit['product_id'] ?? '') ?>">
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input name="product_name" class="form-control" required value="<?= e($edit['product_name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['category_id'] ?>"
                            <?= ($edit['category_id'] ?? 0) == $c['category_id'] ? 'selected' : '' ?>>
                            <?= e($c['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Pet type</label>
                <input name="pet_type" class="form-control" required value="<?= e($edit['pet_type'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Retail price</label>
                <input type="number" step="0.01" min="0.01" name="retail_price" class="form-control" required value="<?= e($edit['retail_price'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Wholesale price</label>
                <input type="number" step="0.01" min="0.01" name="wholesale_price" class="form-control" required value="<?= e($edit['wholesale_price'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Low stock level</label>
                <input type="number" min="0" name="low_stock_level" class="form-control" required value="<?= e($edit['low_stock_level'] ?? 10) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tag</label>
                <input name="tag" class="form-control" value="<?= e($edit['tag'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Image file</label>
                <input name="image" class="form-control" value="<?= e($edit['image'] ?? '') ?>">
                <div class="form-text">File name in assets/images/</div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save</button>
                <?php if ($edit): ?><a href="products.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div id="stock-in">
    <h2 class="h5">Stock In</h2>

    <div class="card shadow-sm mb-4">
        <div class="card-header">Receive Stock</div>
        <div class="card-body">
            <form method="post" class="row g-3">
                <input type="hidden" name="receive" value="1">
                <div class="col-md-5">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-select" required>
                        <?php foreach ($activeProducts as $p): ?>
                            <option value="<?= $p['product_id'] ?>"><?= e($p['product_name']) ?> (stock: <?= $p['stock'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity</label>
                    <input type="number" min="1" name="quantity" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date received</label>
                    <input type="date" name="date_received" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Receive</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">Recent Receipts</div>
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light"><tr><th>Date</th><th>Product</th><th>Qty</th><th>By</th></tr></thead>
                <tbody>
                <?php foreach ($history as $h): ?>
                    <tr>
                        <td><?= e($h['date_received']) ?></td>
                        <td><?= e($h['product_name']) ?></td>
                        <td><?= $h['quantity'] ?></td>
                        <td><?= e($h['received_by']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$history): ?><tr><td colspan="4" class="text-center text-muted">No receipts yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mb-3">
    <h2 class="h5">All Products</h2>
    <?php if (!$products): ?>
        <div class="alert alert-secondary">No products yet.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
            <?php foreach ($products as $p): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= BASE_URL ?>/assets/images/<?= e($p['image']) ?>"
                                 class="card-img-top" alt="<?= e($p['product_name']) ?>"
                                 style="height: 180px; object-fit: cover">
                        <?php else: ?>
                            <div class="bg-light text-muted d-flex align-items-center justify-content-center"
                                 style="height: 180px">No image</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h3 class="h5 card-title"><?= e($p['product_name']) ?></h3>
                                <span class="badge bg-<?= $p['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </div>
                            <p class="text-muted mb-2"><?= e($p['category_name']) ?> | <?= e($p['pet_type']) ?></p>
                            <?php if (!empty($p['tag'])): ?>
                                <span class="badge bg-danger mb-2"><?= e($p['tag']) ?></span>
                            <?php endif; ?>
                            <p class="mb-1">Retail: <strong>₱<?= number_format($p['retail_price'], 2) ?></strong></p>
                            <p class="mb-1">Wholesale: ₱<?= number_format($p['wholesale_price'], 2) ?></p>
                            <p class="mb-0">Stock: <?= (int) $p['stock'] ?></p>
                        </div>
                        <div class="card-footer bg-white d-flex gap-2">
                            <a href="?edit=<?= (int) $p['product_id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="post">
                                <input type="hidden" name="toggle" value="<?= (int) $p['product_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                    <?= $p['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
