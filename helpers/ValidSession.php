<?php

require_once __DIR__ . '/../app/controllers/UserController.php';

class Helper
{
    public static function validSession($role, $redirect)
    {
        $is_valid = UserController::isRole($role);

        if (!$is_valid) {
            header("Location: " . $redirect);
            exit();
        }
    }
}
