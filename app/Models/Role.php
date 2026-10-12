<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Role extends BaseModel
{
    protected static string $table = 'roles';
    protected static array $fillable = ['name', 'description'];
}
