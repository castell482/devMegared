<?php

require_once __DIR__ . '/../../app/core/Database.php';

return new class
{
    public static function up()
    {
        $db = Database::getInstance()->getConnection();
        $query = "CREATE TABLE IF NOT EXISTS role (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            description TEXT NOT NULL
        )";
        $db->exec($query);
    }

    public static function down()
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("DROP TABLE IF EXISTS role");
    }
};
