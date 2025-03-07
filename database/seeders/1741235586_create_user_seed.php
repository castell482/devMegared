<?php

require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Role.php';
require_once __DIR__ . '/../../app/models/User.php';

return new class
{
    public static function up()
    {
        User::create([
            'role_id' => 2,
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => 'Qwerty12345*'
        ]);
    }

    public static function down()
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("DELETE FROM user WHERE email = 'customer@example.com'");
    }
};
