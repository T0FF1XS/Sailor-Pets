<?php

$user = current_user();
$role = $user['role'] ?? null;
$staffPage = $staffPage ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ($staffPage): ?>
        <title><?= e(($pageTitle ?? 'Staff') . " - Sailor's Pet Supplies") ?></title>
    <?php else: ?>
        <title><?= e($pageTitle ?? "Sailor's Pet Supplies") ?></title>
    <?php endif; ?>
    <link href="<?= BASE_URL ?>/assets/bootstrap5/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php if ($staffPage): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 d-print-none">
    <div class="container">
        <span class="navbar-brand">Sailor's Pet Supplies <small class="text-secondary">Staff</small></span>
        <?php if ($user): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#staffNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="staffNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/dashboard.php">Dashboard</a></li>
                    <?php if (in_array($role, ['Owner', 'Sales Staff'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/inquiries.php">Inquiries</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/pos.php">POS</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/sales.php">Sales</a></li>
                    <?php endif; ?>
                    <?php if (in_array($role, ['Owner', 'Inventory Staff'])): ?>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/products.php#stock-in">Stock In</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/sensors.php">Sensors</a></li>
                </ul>
                <span class="navbar-text me-3"><?= e($user['name']) ?> (<?= e($role) ?>)</span>
                <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/login.php?logout=1">Logout</a>
            </div>
        <?php else: ?>
            <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/index.php">View store</a>
        <?php endif; ?>
    </div>
</nav>
<?php else: ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 d-print-none">
    <div class="container">
        <a class="navbar-brand" href="<?= BASE_URL ?>/index.php">Sailor's Pet Supplies</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php">Catalog</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/login.php">Staff Login</a></li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="container pb-5">
<?php if ($msg = get_flash()): ?>
    <div class="alert alert-info"><?= e($msg) ?></div>
<?php endif; ?>
