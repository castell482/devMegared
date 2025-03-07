<?php

require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/AccountRepository.php';
require_once __DIR__ . '/TransactionLogRepository.php';

class TransactionRepository
{
    private static function create($from_account, $to_account, $amount, $type, $description)
    {
        return Transaction::create([
            'account_id' => $from_account,
            'related_account_id' => $to_account,
            'amount' => $amount,
            'transaction_type' => $type,
            'description' => $description
        ]);
    }

    public static function  transfer($from_account, $to_account, $amount)
    {
        $transaction_id = null;
        try {
            AccountRepository::movement($from_account, $to_account, $amount);
            $transaction_id = static::create($from_account, $to_account, $amount, 'TRANSFER', 'Transfer to account ' . $to_account . ' with amount ' . $amount . ' from account ' . $from_account . '.')->id;
        } catch (Exception $e) {
            throw new Exception('Transfer failed.');
        } finally {
            TransactionLogRepository::create($transaction_id, $amount, 'TRANSFER');
        }
    }

    public static function deposit($to_account, $amount)
    {
        $transaction_id = null;
        try {
            AccountRepository::deposit($to_account, $amount);
            $transaction_id = static::create($to_account, null, $amount, 'DEPOSIT', 'Deposit to account ' . $to_account . ' with amount ' . $amount . '.');
        } catch (Exception $e) {
            throw new Exception('Deposit failed.');
        } finally {
            TransactionLogRepository::create($transaction_id, $amount, 'DEPOSIT');
        }
    }

    public static function transferHistory()
    {
        $account = AccountRepository::account();
        return Transaction::findAllByCondition(['account_id' => $account->id]);
    }

    public static function depositHistory()
    {
        $account = AccountRepository::account();
        return Transaction::findAllByCondition(['related_account_id' => $account->id]);
    }
}
