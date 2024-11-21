<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'last_name',
        'first_name',
        'hire_date',
        'status',
        'salary',
        'birthday',
        'notes',
    ];

    public function works()
    {
        return $this->hasMany(Work::class);
    }

    public function breakings()
    {
        return $this->hasManyThrough(Breaking::class, Work::class);
    }

    public function PaidHolidays()
    {
        return $this->hasMany(PaidHoliday::class);
    }
}
