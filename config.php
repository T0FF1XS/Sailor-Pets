<?php

session_start();

const DB_HOST = 'localhost';
const DB_NAME = 'sailor_pets';
const DB_USER = 'root';
const DB_PASS = '';

const BASE_URL = '/sailor_pets';

const SENSOR_KEY = 'change-this-key';

const TEMP_MAX = 30.0;
const HUMIDITY_MAX = 70.0;

const OWNER_EMAIL = 'owner@sailorpets.local';
const OWNER_PASSWORD = 'owner123';

function run($sql, $values = [])
{
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($values) {
        $stmt->bind_param(str_repeat('s', count($values)), ...$values);
    }
    $stmt->execute();
    return $stmt;
}

function get_rows($sql, $values = [])
{
    return run($sql, $values)->get_result()->fetch_all(MYSQLI_ASSOC);
}

function get_row($sql, $values = [])
{
    $rows = get_rows($sql, $values);
    return $rows[0] ?? null;
}

function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function current_user()
{
    return $_SESSION['user'] ?? null;
}

function redirect($page)
{
    header('Location: ' . BASE_URL . '/' . $page);
    exit;
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
        redirect('login.php');
    }

    if (!in_array($user['role'], $roles)) {
        exit('Access denied.');
    }

    return $user;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() != 1049) {
        throw $e;
    }
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    $conn->query('CREATE DATABASE `' . DB_NAME . '`');
    $conn->select_db(DB_NAME);
}
$conn->set_charset('utf8mb4');

$count = get_row('SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = ?', [DB_NAME]);

if ($count['total'] == 0) {
    $sql = file_get_contents(__DIR__ . '/sql/sailor_pets.sql');
    $sql = preg_replace('/^(DROP DATABASE|CREATE DATABASE|USE) .*$/mi', '', $sql);
    $sql = preg_replace('/^DELIMITER .*$/mi', '', $sql);
    $sql = str_replace('END//', 'END;', $sql);
    $conn->multi_query($sql);
    while ($conn->more_results()) {
        $conn->next_result();
    }

    $role = get_row('SELECT role_id FROM roles WHERE role_name = ?', ['Owner']);
    run(
        'INSERT INTO users (role_id, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)',
        [$role['role_id'], 'Store', 'Owner', OWNER_EMAIL, password_hash(OWNER_PASSWORD, PASSWORD_DEFAULT)]
    );
}
