<?php

class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';

        try {
            if (!empty($config['url'])) {
                $dsn = $config['url'];
            } else {
                $dsn = "mysql:host={$config['host']}";
                if (!empty($config['port'])) {
                    $dsn .= ";port={$config['port']}";
                }
                $dsn .= ";dbname={$config['database']}";
                if (!empty($config['charset'])) {
                    $dsn .= ";charset={$config['charset']}";
                }
                if (!empty($config['unix_socket'])) {
                    $dsn .= ";unix_socket={$config['unix_socket']}";
                }
            }

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true
            ];

            if (!empty($config['options']) && is_array($config['options'])) {
                $options = array_merge($options, $config['options']);
            }

            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);

            if (isset($config['strict']) && $config['strict']) {
                $this->pdo->exec('SET SESSION sql_mode=\'STRICT_ALL_TABLES\'');
            }
        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }
}
