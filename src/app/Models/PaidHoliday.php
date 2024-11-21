<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidHoliday extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'employee_id',
        'date',
        'type',
        'reason',
        'status',
        'count',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
