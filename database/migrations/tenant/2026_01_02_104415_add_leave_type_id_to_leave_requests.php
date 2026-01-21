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
        Schema::connection('tenant')->table('leave_requests', function (Blueprint $table) {
            $table->foreignId('leave_type_id')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::connection('tenant')->table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('leave_type_id');
        });
    }
};
