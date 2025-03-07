<?php

require_once __DIR__ . '/../../app/core/Database.php';

return new class
{
    public static function up()
    {
        $db = Database::getInstance()->getConnection();
        $query = "CREATE TABLE IF NOT EXISTS transaction_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_id INT NULL,
            user_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            status ENUM('SUCCESS', 'FAILED') NOT NULL,
            transaction_type ENUM('DEPOSIT', 'TRANSFER') NOT NULL,
            ip_address VARCHAR(255) NOT NULL,
            user_agent TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (transaction_id) REFERENCES transaction(id),
            FOREIGN KEY (user_id) REFERENCES user(id)
        )";
        $db->exec($query);
    }

    public static function down()
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("DROP TABLE IF EXISTS transaction_log");
    }
};
