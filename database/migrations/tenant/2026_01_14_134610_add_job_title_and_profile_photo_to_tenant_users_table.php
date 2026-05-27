<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    protected $connection = 'tenant'; // ✅ REQUIRED (like activity_logs)

    public function up(): void
    {
        if (!Schema::connection('tenant')->hasColumn('users', 'job_title')) {
            Schema::connection('tenant')->table('users', function (Blueprint $table) {
                $table->string('job_title')->nullable()->after('role');
            });
        }

        if (!Schema::connection('tenant')->hasColumn('users', 'profile_photo_path')) {
            Schema::connection('tenant')->table('users', function (Blueprint $table) {
                $table->string('profile_photo_path')->nullable()->after('job_title');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('tenant')->table('users', function (Blueprint $table) {
            if (Schema::connection('tenant')->hasColumn('users', 'profile_photo_path')) {
                $table->dropColumn('profile_photo_path');
            }

            if (Schema::connection('tenant')->hasColumn('users', 'job_title')) {
                $table->dropColumn('job_title');
            }
        });
    }
};
