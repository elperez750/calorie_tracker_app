<?php

function get_db_connection() {
    static $db = null;

    if ($db !== null) {
        return $db;
    }

    $config = require __DIR__ . '/db_config.php';
    $dsn = 'mysql:host=' . $config['host']
        . ';dbname=' . $config['dbname']
        . ';charset=utf8mb4';

    if (!empty($config['unix_socket'])) {
        $dsn .= ';unix_socket=' . $config['unix_socket'];
    }

    try {
        $db = new PDO($dsn, $config['username'], $config['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        return false;
    }
}

function require_db_connection() {
    $db = get_db_connection();
    if ($db === false) {
        include __DIR__ . '/../view/database_error.php';
        exit;
    }
    return $db;
}
