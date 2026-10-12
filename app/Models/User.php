<?php

namespace App\Models;

use App\Helpers\BaseModel;

class User extends BaseModel
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name', 'email', 'password', 'role_id', 'status', 'avatar', 'department_segment',
    ];
    protected static array $searchable = [
        'name', 'email', 'department_segment',
    ];
}
