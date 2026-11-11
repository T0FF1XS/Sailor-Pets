<?php

function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function url($page, $params = [])
{
    $link = BASE_URL . '/index.php?page=' . $page;
    foreach ($params as $key => $value) {
        $link .= '&' . $key . '=' . urlencode($value);
    }
    return $link;
}

function redirect($page, $params = [], $anchor = '')
{
    header('Location: ' . url($page, $params) . $anchor);
    exit;
}

function slug($text)
{
    $text = strtolower($text);
    $text = str_replace(['&', '/', ' '], '-', $text);
    while (strpos($text, '--') !== false) {
        $text = str_replace('--', '-', $text);
    }
    return trim($text, '-');
}

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function photo_url($path)
{
    if (!$path) {
        return '';
    }
    $dot = strrpos($path, '.');
    $base = ($dot === false) ? $path : substr($path, 0, $dot);
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        if (file_exists(__DIR__ . '/../assets/images/' . $base . '.' . $ext)) {
            return BASE_URL . '/assets/images/' . $base . '.' . $ext;
        }
    }
    return '';
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function flash($message)
{
    $_SESSION['flash'] = $message;
}

function get_flash()
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function require_role($roles)
{
    $user = current_user();

    if (!$user) {
        redirect('login');
    }

    if (!in_array($user['role'], $roles)) {
        exit('Access denied.');
    }

    return $user;
}

function render($view, $data = [], $layout = 'staff')
{
    extract($data);
    $staffPage = ($layout == 'staff');
    if ($staffPage) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }
    $pageTitle = $pageTitle ?? 'Sailor Pets';
    include __DIR__ . '/../views/layout/header.php';
    include __DIR__ . '/../views/' . $view . '.php';
    include __DIR__ . '/../views/layout/footer.php';
}
