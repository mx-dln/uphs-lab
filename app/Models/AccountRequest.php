<?php

namespace App\Models;

use App\Helpers\BaseModel;

class AccountRequest extends BaseModel
{
    protected static string $table = 'account_requests';
    protected static array $fillable = [
        'request_number', 'name', 'email', 'requested_role_id',
        'department_segment', 'id_number', 'reason', 'status',
        'reviewed_by', 'admin_notes',
    ];
    protected static array $searchable = [
        'request_number', 'name', 'email', 'department_segment', 'id_number',
    ];
}
