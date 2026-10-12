<?php

namespace App\Models;

use App\Helpers\BaseModel;

class EquipmentDispatch extends BaseModel
{
    protected static string $table = 'equipment_dispatches';
    protected static array $fillable = [
        'equipment_id', 'from_lab_id', 'to_lab_id', 'dispatched_to_user_id',
        'segment', 'dispatch_date', 'return_due_date', 'actual_return_date',
        'purpose', 'status', 'notes', 'dispatched_by',
    ];
    protected static array $searchable = [
        'segment', 'purpose', 'status',
    ];
}
