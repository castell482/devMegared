<?php

require_once __DIR__ . '/../models/TransactionLog.php';
require_once __DIR__ . '/../services/UserService.php';

class TransactionLogRepository
{
    public static function create($transaction_id, $amount, $transaction_type)
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $user_id = UserService::user()->id;

        return TransactionLog::create([
            'transaction_id' => $transaction_id,
            'user_id' => $user_id,
            'amount' => $amount,
            'status' => $transaction_id ? 'SUCCESS' : 'FAILED',
            'transaction_type' => $transaction_type,
            'ip_address' => $ip,
            'user_agent' => $user_agent
        ]);
    }
}
