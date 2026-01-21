<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'db_name',
        'db_username',
        'db_password',
        'db_host',
        'db_port',
        'status',
        'company_logo_path',
    ];
}
