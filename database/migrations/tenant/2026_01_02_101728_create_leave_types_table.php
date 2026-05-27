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
        if (Schema::connection('tenant')->hasTable('leave_types')) {
            return;
        }

        Schema::connection('tenant')->create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // EL, CL, SL
            $table->integer('yearly_quota');     // 12, 6, 8
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('leave_types');
    }
};
