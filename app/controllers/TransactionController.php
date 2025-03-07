<?php

require_once __DIR__ . '/../services/TransactionService.php';

class TransactionController
{
    public static function transfer($from_account, $to_account, $amount)
    {
        return TransactionService::create($from_account, $to_account, $amount)->transfer();
    }

    public static function deposit($to_account, $amount)
    {
        return TransactionService::create(null, $to_account, $amount)->deposit();
    }

    public static function transferHistory()
    {
        return TransactionService::transferHistory();
    }

    public static function depositHistory()
    {
        return TransactionService::depositHistory();
    }

    public static function history()
    {
        $transferHistory = static::transferHistory();
        $depositHistory = static::depositHistory();
        $history = array_merge($transferHistory, $depositHistory);

        foreach ($history as &$transaction) {
            if (isset($transaction->account_id)) {
                $fromAccount = Account::find($transaction->account_id);
                $transaction->from_user = $fromAccount ? User::find($fromAccount->user_id)->name : 'N/A';
            } else {
                $transaction->from_user = 'N/A';
            }

            if (isset($transaction->related_account_id)) {
                $toAccount = Account::find($transaction->related_account_id);
                $transaction->to_user = $toAccount ? User::find($toAccount->user_id)->name : 'N/A';
            } else {
                $transaction->to_user = 'N/A';
            }
        }

        usort($history, function ($a, $b) {
            return strtotime($b->created_at) - strtotime($a->created_at);
        });

        return $history;
    }
}
