<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // 🔴 Tenant database
    protected $connection = 'tenant';

    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'assigned_user_id')) {
                $table->dropColumn('assigned_user_id');
            }

            if (Schema::hasColumn('tasks', 'assigned_team_id')) {
                $table->dropColumn('assigned_team_id');
            }
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('assigned_user_id')->nullable();
            $table->foreignId('assigned_team_id')->nullable();
        });
    }
};
