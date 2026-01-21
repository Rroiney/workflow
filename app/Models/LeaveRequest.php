<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'from_date',
        'to_date',
        'reason',
        'status',
        'approved_by',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class);
    }

    public function approver()
    {
        return $this->belongsTo(TenantUser::class, 'approved_by');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
