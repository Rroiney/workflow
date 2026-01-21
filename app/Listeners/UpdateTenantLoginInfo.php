<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Database\Eloquent\Model;

class UpdateTenantLoginInfo
{
    public function handle(Login $event): void
    {
        if ($event->guard !== 'tenant') {
            return;
        }

        if ($event->user instanceof Model) {
            $event->user->fill([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ])->save();
        }
    }
}
