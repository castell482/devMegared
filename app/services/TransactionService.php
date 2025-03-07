<?php

require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/../repositories/TransactionRepository.php';

class TransactionService
{
    public static function create($from_account, $to_account, $amount)
    {
        return new class($from_account, $to_account, $amount)
        {
            private $from_account;
            private $to_account;
            private $amount;

            public function __construct($from_account, $to_account, $amount)
            {
                $this->from_account = $from_account;
                $this->to_account = $to_account;
                $this->amount = $amount;
            }

            public function transfer()
            {
                return TransactionRepository::transfer($this->from_account, $this->to_account, $this->amount);
            }

            public function deposit()
            {
                UserService::validateRole('admin');
                return TransactionRepository::deposit($this->to_account, $this->amount);
            }
        };
    }

    public static function transferHistory()
    {
        return TransactionRepository::transferHistory();
    }

    public static function depositHistory()
    {
        return TransactionRepository::depositHistory();
    }
}
