<?php

require_once __DIR__ . '/../core/Model.php';

class TransactionLog extends Model
{
    protected $table = 'transaction_log';

    public $id;
    public $transaction_id;
    public $user_id;
    public $amount;
    public $status;
    public $transaction_type;
    public $ip_address;
    public $user_agent;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'amount',
        'status',
        'transaction_type',
        'ip_address',
        'user_agent',
    ];
}
