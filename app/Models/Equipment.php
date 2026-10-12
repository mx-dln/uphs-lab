<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Equipment extends BaseModel
{
    protected static string $table = 'equipment';
    protected static array $fillable = [
        'asset_tag', 'property_code', 'name', 'category_id', 'classification',
        'brand', 'model', 'serial_number', 'laboratory_id', 'acquisition_date',
        'purchase_cost', 'status', 'service_status', 'notes', 'qr_code',
    ];
    protected static array $searchable = [
        'asset_tag', 'property_code', 'name', 'serial_number', 'brand', 'model', 'classification',
    ];
}
