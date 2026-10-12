<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Attendance extends BaseModel
{
    protected static string $table = 'attendance';
    protected static array $fillable = [
        'student_id', 'laboratory_id', 'attendance_date', 'time_in',
        'ip_address', 'browser', 'session_id', 'status',
    ];
}
