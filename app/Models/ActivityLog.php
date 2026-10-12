<?php

namespace App\Models;

use App\Helpers\BaseModel;

class ActivityLog extends BaseModel
{
    protected static string $table = 'activity_logs';
    protected static array $fillable = [
        'user_id', 'action', 'description', 'ip_address', 'user_agent',
    ];
}
