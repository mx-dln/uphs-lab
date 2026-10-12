<?php

namespace App\Models;

use App\Helpers\BaseModel;

class AuditItem extends BaseModel
{
    protected static string $table = 'audit_items';
    protected static array $fillable = [
        'audit_id', 'equipment_id', 'status', 'scanned_at', 'notes',
    ];
}
