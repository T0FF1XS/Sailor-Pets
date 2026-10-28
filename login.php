<?php

include 'config.php';

if (isset($_GET['logout'])) {
    session_destroy();
    redirect('index.php');
}

if (current_user()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $found = get_row(
        'SELECT u.user_id, u.first_name, u.last_name, u.password, r.role_name
         FROM users u JOIN roles r ON u.role_id = r.role_id
         WHERE u.email = ?',
        [trim($_POST['email'])]
    );

    if ($found && password_verify($_POST['password'], $found['password'])) {
        $_SESSION['user'] = [
            'user_id' => $found['user_id'],
            'name' => $found['first_name'] . ' ' . $found['last_name'],
            'role' => $found['role_name'],
        ];
        redirect('dashboard.php');
    }

    $error = 'Wrong email or password.';
}

$staffPage = true;
$pageTitle = 'Staff Login';
include 'header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4 mb-3">Staff Login</h1>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
