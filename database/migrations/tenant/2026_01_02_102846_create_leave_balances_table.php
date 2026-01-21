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
        Schema::connection('tenant')->create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('leave_type_id');
            $table->integer('balance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::connection('tenant')->dropIfExists('leave_balances');
    }
};
