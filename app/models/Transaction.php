<?php

require_once __DIR__ . '/../core/Model.php';

class Transaction extends Model
{
    protected $table = 'transaction';

    public $id;
    public $account_id;
    public $related_account_id;
    public $amount;
    public $transaction_type;
    public $description;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    protected $fillable = [
        'account_id',
        'related_account_id',
        'amount',
        'transaction_type',
        'description'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }
}
