<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TenantUser;

class Document extends Model
{
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
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
