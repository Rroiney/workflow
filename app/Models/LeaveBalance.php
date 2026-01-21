<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'balance',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
