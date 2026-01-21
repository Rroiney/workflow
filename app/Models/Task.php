<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'title',
        'description',
        'status',
        'created_by',
    ];

    public function users()
    {
        return $this->belongsToMany(
            TenantUser::class,
            'task_user',
            'task_id',
            'user_id'
        );
    }


    public function creator()
    {
        return $this->belongsTo(TenantUser::class, 'created_by');
    }
}
