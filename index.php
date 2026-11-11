<?php

include 'config.php';

date_default_timezone_set(TIMEZONE);
ini_set('display_errors', SHOW_ERRORS ? '1' : '0');

include 'core/database.php';
include 'core/helpers.php';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

spl_autoload_register(function ($class) {
    foreach (['controllers', 'models'] as $folder) {
        $file = __DIR__ . '/' . $folder . '/' . $class . '.php';
        if (file_exists($file)) {
            include $file;
            return;
        }
    }
});

$routes = [
    'home' => ['StoreController', 'home'],
    'inquiry_send' => ['StoreController', 'send'],
    'inquiry_sent' => ['StoreController', 'sent'],
    'track' => ['StoreController', 'track'],

    'login' => ['AuthController', 'login'],
    'login_check' => ['AuthController', 'check'],
    'logout' => ['AuthController', 'logout'],

    'dashboard' => ['DashboardController', 'index'],
    'alert_read' => ['DashboardController', 'markRead'],

    'inquiries' => ['InquiryController', 'index'],
    'inquiry_decide' => ['InquiryController', 'decide'],

    'pos' => ['PosController', 'index'],
    'pos_checkout' => ['PosController', 'checkout'],

    'sales' => ['SaleController', 'index'],

    'products' => ['ProductController', 'index'],
    'product_save' => ['ProductController', 'save'],
    'product_toggle' => ['ProductController', 'toggle'],
    'stock_receive' => ['ProductController', 'receive'],

    'categories' => ['CategoryController', 'index'],
    'category_save' => ['CategoryController', 'save'],
    'category_delete' => ['CategoryController', 'delete'],

    'staff' => ['StaffController', 'index'],
    'staff_save' => ['StaffController', 'save'],
    'staff_delete' => ['StaffController', 'delete'],

    'rooms' => ['RoomController', 'index'],
    'room_save' => ['RoomController', 'save'],
    'room_delete' => ['RoomController', 'delete'],

    'sensors' => ['SensorController', 'index'],
    'sensor_record' => ['SensorController', 'record'],
];

$page = $_GET['page'] ?? 'home';

if (!isset($routes[$page])) {
    http_response_code(404);
    exit('Page not found.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $page != 'sensor_record') {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Invalid request. Go back, refresh the page and try again.');
    }
}

[$class, $method] = $routes[$page];
(new $class)->$method();
