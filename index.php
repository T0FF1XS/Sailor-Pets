<?php

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['customer_name']);
    $phone = trim($_POST['customer_phone']);
    $email = trim($_POST['customer_email']);
    $address = trim($_POST['customer_address']);
    $message = substr(trim($_POST['message']), 0, 255);
    $type = ($_POST['inquiry_type'] == 'Wholesale') ? 'Wholesale' : 'Retail';
    $column = ($type == 'Wholesale') ? 'wholesale_price' : 'retail_price';

    $items = [];
    foreach ($_POST['qty'] ?? [] as $productId => $quantity) {
        if ((int) $quantity > 0) {
            $items[(int) $productId] = (int) $quantity;
        }
    }

    $error = '';
    if ($name == '' || $phone == '') {
        $error = 'Please enter your name and phone number.';
    } elseif ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!$items) {
        $error = 'Select at least one product.';
    }

    if ($error == '') {
        try {
            $conn->begin_transaction();

            run(
                'INSERT INTO inquiries (customer_name, customer_phone, customer_email, customer_address, inquiry_type, message)
                 VALUES (?, ?, ?, ?, ?, ?)',
                [$name, $phone, $email ?: null, $address ?: null, $type, $message]
            );
            $inquiryId = $conn->insert_id;

            foreach ($items as $productId => $quantity) {
                $product = get_row("SELECT $column AS price FROM products WHERE product_id = ? AND is_active = 1", [$productId]);
                if ($product) {
                    run(
                        'INSERT INTO inquiry_items (inquiry_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)',
                        [$inquiryId, $productId, $quantity, $product['price']]
                    );
                }
            }

            $conn->commit();
            redirect('index.php?sent=' . $inquiryId);
        } catch (mysqli_sql_exception $e) {
            $conn->rollback();
            $error = 'Could not send the inquiry. Please try again.';
        }
    }

    flash($error);
    redirect('index.php');
}

$q = trim($_GET['q'] ?? '');
$cat = (int) ($_GET['category'] ?? 0);

$sql = 'SELECT p.*, c.category_name FROM products p
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.is_active = 1';
$values = [];
if ($q != '') {
    $sql .= ' AND p.product_name LIKE ?';
    $values[] = '%' . $q . '%';
}
if ($cat > 0) {
    $sql .= ' AND p.category_id = ?';
    $values[] = $cat;
}
$sql .= ' ORDER BY c.category_name, p.product_name';

$products = get_rows($sql, $values);
$categories = get_rows('SELECT * FROM categories ORDER BY category_name');

$sentId = (int) ($_GET['sent'] ?? 0);

$pageTitle = $sentId ? 'Inquiry Sent' : 'Catalog';
include 'header.php';
?>

<?php if ($sentId): ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm text-center">
            <div class="card-body py-5">
                <h1 class="h3">Inquiry Sent</h1>
                <p class="mb-1">Your inquiry number is <strong>#<?= $sentId ?></strong>.</p>
                <p class="text-muted">Our staff will review it and contact you using the phone number or email you provided.</p>
                <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Back to catalog</a>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<h1 class="h3 mb-3">Product Catalog</h1>

<form method="get" class="row g-2 mb-3">
    <div class="col-md-5">
        <input name="q" class="form-control" placeholder="Search products" value="<?= e($q) ?>">
    </div>
    <div class="col-md-4">
        <select name="category" class="form-select">
            <option value="0">All categories</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['category_id'] ?>" <?= $cat == $c['category_id'] ? 'selected' : '' ?>>
                    <?= e($c['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>

<form method="post">
    <?php if (!$products): ?>
        <div class="alert alert-secondary">No products found.</div>
    <?php endif; ?>

    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 mb-4">
        <?php foreach ($products as $p): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="position-relative">
                        <?php if ($p['image']): ?>
                            <img src="<?= BASE_URL ?>/assets/images/<?= e($p['image']) ?>" class="card-img-top"
                                 alt="<?= e($p['product_name']) ?>" style="height: 160px; object-fit: cover">
                        <?php else: ?>
                            <div class="bg-light text-muted d-flex align-items-center justify-content-center"
                                 style="height: 160px">No image</div>
                        <?php endif; ?>
                        <?php if ($p['tag']): ?>
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2"><?= e($p['tag']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <div class="small text-muted"><?= e($p['category_name']) ?> | <?= e($p['pet_type']) ?></div>
                        <h6 class="card-title"><?= e($p['product_name']) ?></h6>
                        <div class="text-danger fw-bold fs-5">₱<?= number_format($p['retail_price'], 2) ?></div>
                        <div class="small text-muted">Wholesale: ₱<?= number_format($p['wholesale_price'], 2) ?></div>
                    </div>

                    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                        <?php if ($p['stock'] > 0): ?>
                            <small class="text-muted"><?= e($p['stock']) ?> left</small>
                        <?php else: ?>
                            <span class="badge bg-danger">Out of stock</span>
                        <?php endif; ?>
                        <input type="number" min="0" max="<?= (int) $p['stock'] ?>" value="0" name="qty[<?= $p['product_id'] ?>]"
                               class="form-control form-control-sm" style="width: 80px" aria-label="Quantity for <?= e($p['product_name']) ?>"
                               <?= $p['stock'] < 1 ? 'disabled' : '' ?>>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Send Inquiry</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name *</label>
                    <input name="customer_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone *</label>
                    <input name="customer_phone" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="customer_email" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input name="customer_address" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Order type</label>
                    <select name="inquiry_type" class="form-select">
                        <option>Retail</option>
                        <option>Wholesale</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="2" maxlength="255" placeholder="Optional"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Submit inquiry</button>
        </div>
    </div>
</form>

<?php endif; ?>

<?php include 'footer.php'; ?>
