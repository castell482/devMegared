<?php

require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Role.php';

return new class
{
    public static function up()
    {
        Role::create([
            'name' => 'admin',
            'description' => 'Admin role'
        ]);
    }

    public static function down()
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("DELETE FROM role WHERE name = 'admin'");
    }
};
