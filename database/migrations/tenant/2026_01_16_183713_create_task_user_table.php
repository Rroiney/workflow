<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // 🔴 REQUIRED: ensure it runs on tenant DB
    protected $connection = 'tenant';

    public function up()
    {
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->cascadeOnDelete();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['task_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_user');
    }
};
