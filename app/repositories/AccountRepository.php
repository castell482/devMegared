<?php

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../models/Account.php';

class AccountRepository
{
    private static function updateBalance($user_id, $amount, $type, $with_transaction = true)
    {
        $account = Account::findByConditionOrFail(['user_id' => $user_id], true);

        if ($type === 'increase') {
            $account->balance -= $amount;
        } else {
            $account->balance += $amount;
        }

        return Account::update($account->id, ['balance' => $account->balance], $with_transaction);
    }

    public static function create($user_id, $balance, $with_transaction = true)
    {
        return Account::create(['user_id' => $user_id, 'balance' => $balance], $with_transaction);
    }

    public static function movement($from_account, $to_account, $amount)
    {
        $from_account = Account::findOrFail($from_account);
        $to_account = Account::findOrFail($to_account);

        if ($from_account->balance < $amount) {
            return false;
        }

        Model::executeTransaction([
            'from_account' => function () use ($from_account, $amount) {
                return static::updateBalance($from_account->user_id, $amount, 'decrease', false);
            },
            'to_account' => function () use ($to_account, $amount) {
                return static::updateBalance($to_account->user_id, $amount, 'increase', false);
            }
        ]);

        return true;
    }

    public static function deposit($to_account, $amount)
    {
        $to_account = Account::findOrFail($to_account);

        static::updateBalance($to_account->user_id, $amount, 'increase');

        return true;
    }

    public static function accountOrFail($user_id = null)
    {
        $user_id = $user_id ?: UserService::user()->id;
        return Account::findByConditionOrFail(['user_id' => $user_id], true);
    }

    public static function account($user_id = null)
    {
        $user_id = $user_id ?: UserService::user()->id;
        $accounts = Account::findByCondition(['user_id' => $user_id], true);
        return !empty($accounts) ? $accounts : null;
    }
}
