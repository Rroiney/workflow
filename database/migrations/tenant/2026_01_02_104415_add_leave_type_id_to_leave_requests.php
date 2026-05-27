<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::connection('tenant')->hasColumn('leave_requests', 'leave_type_id')) {
            Schema::connection('tenant')->table('leave_requests', function (Blueprint $table) {
                $table->foreignId('leave_type_id')->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::connection('tenant')->hasColumn('leave_requests', 'leave_type_id')) {
            Schema::connection('tenant')->table('leave_requests', function (Blueprint $table) {
                $table->dropColumn('leave_type_id');
            });
        }
    }
};
