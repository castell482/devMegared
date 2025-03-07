<?php

require_once __DIR__ . '/../core/Model.php';

class Role extends Model
{
    protected $table = 'role';

    public $id;
    public $name;
    public $description;

    protected $fillable = [
        'name',
        'description',
    ];
}
