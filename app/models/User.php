<?php

require_once __DIR__ . '/../core/Model.php';

class User extends Model
{
    protected $table = 'user';

    public $id;
    public $role_id;
    public $account;
    public $name;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
    ];

    protected function casts()
    {
        return [
            'password' => 'hashed'
        ];
    }
}
