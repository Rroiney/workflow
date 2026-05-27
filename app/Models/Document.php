<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'uploaded_by',
        'title',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'visibility',
        'assigned_user_id',
        'team_id',
    ];

    public function uploader()
    {
        return $this->belongsTo(TenantUser::class, 'uploaded_by');
    }

    public function assignedUser()
    {
        return $this->belongsTo(TenantUser::class, 'assigned_user_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
