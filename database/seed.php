<?php

require_once __DIR__ . '/../app/core/Database.php';

$db = Database::getInstance()->getConnection();

$db->exec("CREATE TABLE IF NOT EXISTS seeds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seed VARCHAR(255) NOT NULL UNIQUE,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$files = glob(__DIR__ . '/seeders/*.php');

foreach ($files as $file) {
    $className = pathinfo($file, PATHINFO_FILENAME);

    $stmt = $db->prepare("SELECT COUNT(*) FROM seeds WHERE seed = ?");
    $stmt->execute([$className]);
    $alreadyExecuted = $stmt->fetchColumn();

    if (!$alreadyExecuted) {
        echo "Seed: $className...\n";

        $seed = require_once $file;
        $seed::up();

        $stmt = $db->prepare("INSERT INTO seeds (seed) VALUES (?)");
        $stmt->execute([$className]);
    } else {
        echo "Skipping: $className (already executed)\n";
    }
}

echo "Seeding completed.\n";
