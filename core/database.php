<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} catch (mysqli_sql_exception $e) {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        $conn->query('CREATE DATABASE `' . DB_NAME . '`');
        $conn->select_db(DB_NAME);
    } catch (mysqli_sql_exception $e) {
        exit('Cannot connect to the database. Check DB_HOST, DB_NAME, DB_USER and DB_PASS in config.php.');
    }
}

$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '" . DB_TIMEZONE . "'");

$tables = get_row('SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = ?', [DB_NAME]);

if ($tables['total'] == 0) {
    $conn->multi_query(file_get_contents(__DIR__ . '/../sql/sailor_pets.sql') . file_get_contents(__DIR__ . '/../sql/pricelist.sql'));
    while ($conn->more_results()) {
        $conn->next_result();
    }

    $role = get_row('SELECT role_id FROM roles WHERE role_name = ?', ['Owner']);
    run(
        'INSERT INTO users (role_id, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)',
        [$role['role_id'], 'Store', 'Owner', OWNER_EMAIL, password_hash(OWNER_PASSWORD, PASSWORD_DEFAULT)]
    );
}

$conn->query(
    'CREATE TABLE IF NOT EXISTS login_attempts (
        attempt_id INT AUTO_INCREMENT PRIMARY KEY,
        ip VARCHAR(45) NOT NULL,
        attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )'
);
