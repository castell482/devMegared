<?php

require_once __DIR__ . '/../../app/core/Database.php';

return new class
{
    public static function up()
    {
        $db = Database::getInstance()->getConnection();
        $query = "CREATE TABLE IF NOT EXISTS user (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (role_id) REFERENCES role(id)
        )";
        $db->exec($query);
    }

    public static function down()
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("DROP TABLE IF EXISTS user");
    }
};
