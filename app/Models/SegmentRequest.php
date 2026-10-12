<?php

namespace App\Models;

use App\Helpers\BaseModel;

class SegmentRequest extends BaseModel
{
    protected static string $table = 'segment_requests';
    protected static array $fillable = [
        'request_number', 'requester_name', 'requester_email', 'laboratory_id',
        'segment_name', 'purpose', 'request_date', 'start_time', 'end_time',
        'expected_users', 'equipment_needed', 'status', 'admin_notes', 'reviewed_by',
    ];
    protected static array $searchable = [
        'request_number', 'requester_name', 'requester_email', 'segment_name', 'purpose',
    ];
}
