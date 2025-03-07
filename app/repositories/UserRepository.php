<?php

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/Role.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AccountRepository.php';

class UserRepository
{
    private static function create($role, $name, $email, $password, $with_transaction = true)
    {
        $role = UserRepository::findRole($role);
        return User::create(['role_id' => $role->id, 'name' => $name, 'email' => $email, 'password' => $password], $with_transaction);
    }

    public static function all()
    {
        return User::findAll();
    }

    public static function findByIdWithAccount($user_id)
    {
        $user = User::find($user_id);
        $user->account = AccountRepository::account($user->id) ?? null;
        return $user;
    }

    public static function allWithAccount()
    {
        $users = User::findAll();
        foreach ($users as $user) {
            $user->account = AccountRepository::account($user->id) ?? null;
        }
        return $users;
    }

    public static function findRole($role)
    {
        return  Role::findByConditionOrFail(['name' => $role], true);
    }

    public static function roleById($role_id)
    {
        $role = Role::find($role_id);
        return $role->name;
    }

    public static function roleByUser($user_id)
    {
        $user = UserRepository::findById($user_id);
        return UserRepository::roleById($user->role_id);
    }

    public static function findById($id)
    {
        return User::find($id);
    }

    public static function findByEmail($email)
    {
        return User::findByCondition(['email' => $email], true);
    }

    public static function createAdmin($name, $email, $password)
    {
        return static::create('admin', $name, $email, $password);
    }

    public static function createCustomer($name, $email, $password, $balance)
    {
        $results = Model::executeTransaction([
            'user' => function () use ($name, $email, $password) {
                return static::create('customer', $name, $email, $password, false);
            },
            'account' => function ($results) use ($balance) {
                return AccountRepository::create($results['user']->id, $balance, false);
            }
        ]);

        return $results['user'];
    }
}
