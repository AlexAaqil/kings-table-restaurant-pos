<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkShift extends Model
{
    protected $guarded = [];

    protected $casts = [
        'shift_start' => 'datetime',
        'shift_end' => 'datetime',
    ];

    const SHIFTSTATUS = [
        'active',
        'closed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
