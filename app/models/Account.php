<?php

require_once __DIR__ . '/../core/Model.php';

class Account extends Model
{
    protected $table = 'account';

    public $id;
    public $user_id;
    public $balance;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    protected $fillable = [
        'user_id',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }
}
