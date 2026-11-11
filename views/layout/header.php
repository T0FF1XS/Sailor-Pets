<?php

$user = current_user();
$role = $user['role'] ?? null;
$current = $_GET['page'] ?? 'home';

$menu = [
    ['dashboard', '', 'Dashboard', ['Owner', 'Sales Staff', 'Inventory Staff']],
    ['inquiries', '', 'Inquiries', ['Owner', 'Sales Staff']],
    ['pos', '', 'POS', ['Owner', 'Sales Staff']],
    ['sales', '', 'Sales', ['Owner', 'Sales Staff']],
    ['products', '', 'Products', ['Owner', 'Inventory Staff']],
    ['products', '#stock-in', 'Stock In', ['Owner', 'Inventory Staff']],
    ['categories', '', 'Categories', ['Owner', 'Inventory Staff']],
    ['sensors', '', 'Sensors', ['Owner', 'Sales Staff', 'Inventory Staff']],
    ['rooms', '', 'Rooms', ['Owner']],
    ['staff', '', 'Staff', ['Owner']],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Sailor Pets') ?></title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=DM+Sans:wght@400;500;700&family=JetBrains+Mono:wght@500&display=swap">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap5/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap-icons/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
<script src="<?= BASE_URL ?>/assets/bootstrap5/js/bootstrap.bundle.min.js"></script>
</head>
<body class="<?= $staffPage ? 'staff' : '' ?>">

<?php if (!$staffPage): ?>
<div class="bg-navy text-white text-center small py-2">Free delivery over <b class="text-gold">₱1,500</b> — same-day from the harbor</div>
<?php endif; ?>

<nav class="navbar sticky-top bg-body bg-opacity-90 border-bottom d-print-none">
    <div class="container d-flex flex-wrap align-items-center gap-2">

        <a class="navbar-brand brand-link m-0 d-flex align-items-center gap-2" href="<?= $user ? url('dashboard') : url('home') ?>">
            <span class="icon-box rounded-3 bg-primary text-white d-flex align-items-center justify-content-center fs-5"><i class="bi bi-compass"></i></span>
            <span class="lh-1">
                <b class="brand d-block fs-6 text-brand">SAILOR PETS</b>
                <small class="mono text-secondary"><?= $staffPage ? 'Staff' : 'Harborside Supply Co. — Est. 2024' ?></small>
            </span>
        </a>

        <?php if ($staffPage && $user): ?>

            <ul class="nav nav-pills flex-nowrap overflow-auto order-5 order-lg-0 col-12 col-lg-auto">
                <?php foreach ($menu as $item): ?>
                    <?php if (in_array($role, $item[3])): ?>
                        <li class="nav-item"><a class="nav-link rounded-pill fw-bold <?= ($item[1] == '' && $item[0] == $current) ? 'active' : '' ?>" href="<?= url($item[0]) . $item[1] ?>"><?= $item[2] ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <span class="small text-secondary d-none d-lg-inline"><?= e($user['name']) ?> (<?= e($role) ?>)</span>
            <a class="btn btn-outline-dark btn-sm" href="<?= url('logout') ?>">Logout</a>

        <?php elseif ($staffPage): ?>

            <a class="btn btn-outline-dark btn-sm" href="<?= url('home') ?>">View store</a>

        <?php else: ?>

            <ul class="nav nav-pills flex-nowrap overflow-auto order-5 order-md-0 col-12 col-md-auto">
                <li class="nav-item"><a class="nav-link rounded-pill fw-bold" data-nav="home" href="<?= url('home') ?>#shop">Shop</a></li>
                <li class="nav-item"><a class="nav-link rounded-pill fw-bold" data-nav="about" href="<?= url('home') ?>#/about">About</a></li>
                <li class="nav-item"><a class="nav-link rounded-pill fw-bold" data-nav="contact" href="<?= url('home') ?>#/contact">Contact</a></li>
                <li class="nav-item"><a class="nav-link rounded-pill fw-bold" data-nav="faq" href="<?= url('home') ?>#/faq">FAQ</a></li>
            </ul>
            <label class="search order-4 order-md-0 flex-grow-1 flex-md-grow-0 d-flex align-items-center gap-2 bg-white border rounded-pill px-3 text-secondary">
                <i class="bi bi-search"></i>
                <input id="searchBox" type="search" class="form-control border-0 shadow-none px-0 bg-transparent" placeholder="Search food, toys, beds…" aria-label="Search products">
            </label>
            <button class="btn bg-white border rounded-circle round-btn" onclick="showFavs()" aria-label="Saved items"><i class="bi bi-heart"></i></button>
            <button class="btn bg-white border rounded-circle round-btn position-relative" onclick="showCart()" aria-label="Open inquiry list">
                <i class="bi bi-cart3"></i>
                <span id="count" class="position-absolute top-0 end-0 badge rounded-pill bg-primary">0</span>
            </button>

        <?php endif; ?>
    </div>
</nav>

<?php if ($staffPage): ?>
<main class="container pb-5 pt-4">
<?php endif; ?>

<?php if ($msg = get_flash()): ?>
    <div class="container mt-3"><div class="alert alert-info rounded-4"><?= e($msg) ?></div></div>
<?php endif; ?>
