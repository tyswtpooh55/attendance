<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRequest extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'employee_id',
        'date',
        'work_id',
        'work_in',
        'work_out',
        'breakings',
        'reason',
        'action',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
