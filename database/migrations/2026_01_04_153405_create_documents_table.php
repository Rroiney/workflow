<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('uploaded_by');
            $table->string('title');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('size');

            // visibility
            $table->enum('visibility', ['private', 'team', 'org'])->default('private');

            $table->unsignedBigInteger('assigned_user_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
