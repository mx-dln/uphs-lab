<?php

namespace App\Models;

use App\Helpers\BaseModel;

class EquipmentService extends BaseModel
{
    protected static string $table = 'equipment_services';
    protected static array $fillable = [
        'equipment_id', 'service_type', 'service_date', 'technician_name',
        'company_or_dept', 'cost', 'status', 'notes', 'next_service_date',
    ];
    protected static array $searchable = [
        'service_type', 'technician_name', 'company_or_dept', 'status',
    ];
}
