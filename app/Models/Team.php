<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
        'manager_id',
    ];

    // ✅ Team members (employees)
    public function users()
    {
        return $this->belongsToMany(
            TenantUser::class,
            'team_user',
            'team_id',
            'user_id'
        );
    }

    // ✅ Alias (optional, but fine)
    public function members()
    {
        return $this->users();
    }

    // ✅ Team manager
    public function manager()
    {
        return $this->belongsTo(TenantUser::class, 'manager_id');
    }
}
