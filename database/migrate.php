<?php

require_once __DIR__ . '/../app/core/Database.php';

$db = Database::getInstance()->getConnection();

$db->exec("CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL UNIQUE,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$files = glob(__DIR__ . '/migrations/*.php');

foreach ($files as $file) {
    $className = pathinfo($file, PATHINFO_FILENAME);

    $stmt = $db->prepare("SELECT COUNT(*) FROM migrations WHERE migration = ?");
    $stmt->execute([$className]);
    $alreadyExecuted = $stmt->fetchColumn();

    if (!$alreadyExecuted) {
        echo "Migrating: $className...\n";

        $migration = require_once $file;
        $migration::up();

        $stmt = $db->prepare("INSERT INTO migrations (migration) VALUES (?)");
        $stmt->execute([$className]);
    } else {
        echo "Skipping: $className (already executed)\n";
    }
}

echo "Migrations completed.\n";
