<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Laboratory extends BaseModel
{
    protected static string $table = 'laboratories';
    protected static array $fillable = [
        'name', 'room_number', 'building', 'capacity', 'description',
        'qr_code', 'status',
    ];
    protected static array $searchable = [
        'name', 'room_number', 'building',
    ];
}
