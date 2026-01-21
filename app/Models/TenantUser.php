<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class TenantUser extends Authenticatable
{
    protected $connection = 'tenant';
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'job_title',
        'profile_photo_path',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function teams()
    {
        return $this->belongsToMany(
            Team::class,
            'team_user',
            'user_id',
            'team_id'
        );
    }

    public function tasks()
    {
        return $this->belongsToMany(
            Task::class,
            'task_user',
            'user_id',
            'task_id'
        );
    }
}
