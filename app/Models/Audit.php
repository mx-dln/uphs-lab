<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Audit extends BaseModel
{
    protected static string $table = 'audits';
    protected static array $fillable = [
        'laboratory_id', 'conducted_by', 'audit_date',
        'status', 'completed_at', 'notes',
    ];
}
