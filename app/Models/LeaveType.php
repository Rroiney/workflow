<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'yearly_quota',
    ];
}
