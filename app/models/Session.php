<?php

require_once __DIR__ . '/../core/Model.php';

class Session extends Model
{
    protected $table = 'session';

    public $id;
    public $user_id;
    public $session_token;
    public $is_active;
    public $created_at;
    public $expires_at;
    public $ip_address;
    public $user_agent;

    protected $fillable = [
        'user_id',
        'session_token',
        'is_active',
        'expires_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
}
