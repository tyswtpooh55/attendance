<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breaking extends Model
{
    use HasFactory;

    protected $guarded =
    [
        'id',
    ];

    protected $casts =
    [
        'breaking_in' => 'datetime',
        'breaking_out' => 'datetime',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
