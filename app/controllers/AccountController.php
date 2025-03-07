<?php

require_once __DIR__ . '/../repositories/UserRepository.php';

class AccountController
{
    public static function account($user_id = null)
    {
        return AccountRepository::accountOrFail($user_id);
    }
}