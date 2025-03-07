<?php

require_once __DIR__ . '/../../app/core/Database.php';

return new class
{
    public static function up()
    {
        $db = Database::getInstance()->getConnection();
        $query = "CREATE TABLE IF NOT EXISTS transaction (
            id INT AUTO_INCREMENT PRIMARY KEY,
            account_id INT NULL,
            related_account_id INT NOT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            transaction_type ENUM('DEPOSIT', 'TRANSFER') NOT NULL,
            description TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (account_id) REFERENCES account(id),
            FOREIGN KEY (related_account_id) REFERENCES account(id)
        )";
        $db->exec($query);
    }

    public static function down()
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("DROP TABLE IF EXISTS transaction");
    }
};
