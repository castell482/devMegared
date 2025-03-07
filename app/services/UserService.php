<?php

require_once __DIR__ . '/../repositories/UserRepository.php';
require_once __DIR__ . '/SessionService.php';

class UserService
{

    public static function login($email, $password)
    {
        $user = UserRepository::findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) throw new Exception("Invalid email or password.");

        SessionService::create($user->id);

        return true;
    }

    public static function logout()
    {
        SessionService::remove();
    }

    public static function user($user_id = null)
    {
        if ($user_id) {
            UserService::validateRole('admin');
            return UserRepository::findById($user_id);
        }

        $session = SessionService::getCurrent();
        if (!$session) throw new Exception("Unauthorized.");

        $user = UserRepository::findById($session->user_id);
        if (!$user) throw new Exception("Unauthorized.");

        return $user;
    }

    public static function userWithAccount($user_id = null)
    {
        $user = UserService::user($user_id);
        $user->account = AccountRepository::account($user->id) ?? null;
        return $user;
    }

    public static function isRole($role)
    {
        $user  = static::user();
        $role_name = UserRepository::roleByUser($user->id);
        if (!$user || $role_name !== $role) return false;
        return true;
    }

    public static function validateRole($role)
    {
        $user  = static::user();
        $role_name = UserRepository::roleByUser($user->id);
        if (!$user || $role_name !== $role) throw new Exception("Unauthorized.");
        return $user;
    }

    public static function users()
    {
        static::validateRole('admin');
        return UserRepository::all();
    }

    public static function usersWithAccount()
    {
        static::validateRole('admin');
        return UserRepository::allWithAccount();
    }

    public static function register($name, $email, $password)
    {
        return new class($name, $email, $password)
        {
            private $name;
            private $email;
            private $password;

            public function __construct($name, $email, $password)
            {
                $this->name = $name;
                $this->email = $email;
                $this->password = $password;
            }

            public function customer($balance)
            {
                return UserRepository::createCustomer($this->name, $this->email, $this->password, $balance);
            }

            public function admin()
            {
                UserService::validateRole('admin');
                return UserRepository::createAdmin($this->name, $this->email, $this->password);
            }
        };
    }
}
