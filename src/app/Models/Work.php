<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $guarded =
    [
        'id',
    ];

    protected $casts = [
        'work_in' => 'datetime',
        'work_out' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function breakings()
    {
        return $this->hasMany(Breaking::class);
    }
}
