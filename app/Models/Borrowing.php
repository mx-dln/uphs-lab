<?php

namespace App\Models;

use App\Helpers\BaseModel;

class Borrowing extends BaseModel
{
    protected static string $table = 'borrowings';
    protected static array $fillable = [
        'borrower_id', 'equipment_id', 'date_borrowed',
        'expected_return', 'actual_return', 'status', 'approved_by', 'notes',
    ];
}
