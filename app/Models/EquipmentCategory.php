<?php

namespace App\Models;

use App\Helpers\BaseModel;

class EquipmentCategory extends BaseModel
{
    protected static string $table = 'equipment_categories';
    protected static array $fillable = ['name', 'description'];
}
