<?php

require_once __DIR__ . '/../../app/core/Database.php';

return new class
{
    public static function up()
    {
        $db = Database::getInstance()->getConnection();
        $query = "CREATE TABLE IF NOT EXISTS session (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_token VARCHAR(255) NOT NULL UNIQUE,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL,
            ip_address VARCHAR(255) NOT NULL,
            user_agent TEXT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES user(id)
        )";
        $db->exec($query);
    }

    public static function down()
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("DROP TABLE IF EXISTS session");
    }
};

