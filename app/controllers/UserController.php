<?php

require_once __DIR__ . '/../services/UserService.php';

class UserController
{
    public static function login($email, $password)
    {
        return UserService::login($email, $password);
    }

    public static function logout()
    {
        return UserService::logout();
    }

    public static function isRole($role)
    {
        return UserService::isRole($role);
    }

    public static function user($user_id = null)
    {
        return UserService::user($user_id);
    }

    public static function userWithAccount($user_id = null)
    {
        return UserService::userWithAccount($user_id);
    }

    public static function users()
    {
        return UserService::users();
    }

    public static function usersWithAccount()
    {
        return UserService::usersWithAccount();
    }

    public static function register($role, $name, $email, $password, $amount = null)
    {
        $register = UserService::register($name, $email, $password);
        if ($role === 'admin') {
            return $register->admin();
        } else {
            if (!$amount) throw new Exception("Amount is required.");
            return $register->customer($amount);
        }
    }
}
