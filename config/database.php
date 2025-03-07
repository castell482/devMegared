<?php

require_once __DIR__ . '/../app/core/EnvLoader.php';

return [
    'url' => EnvLoader::env('DB_URL'),
    'host' => EnvLoader::env('DB_HOST', '127.0.0.1'),
    'port' => EnvLoader::env('DB_PORT', '3306'),
    'database' => EnvLoader::env('DB_DATABASE', 'bank'),
    'username' => EnvLoader::env('DB_USERNAME', 'developer'),
    'password' => EnvLoader::env('DB_PASSWORD', 'password'),
    'unix_socket' => EnvLoader::env('DB_SOCKET', ''),
    'charset' => EnvLoader::env('DB_CHARSET', 'utf8mb4'),
    'collation' => EnvLoader::env('DB_COLLATION', 'utf8mb4_unicode_ci'),
    'strict' => true,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => EnvLoader::env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
];
