<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Investigation extends BaseModel
{
    protected static string $table = 'investigations';
    protected static array $fillable = [
        'equipment_id', 'audit_id', 'initiated_by',
        'status', 'findings', 'recommendations', 'resolved_at',
    ];
}
