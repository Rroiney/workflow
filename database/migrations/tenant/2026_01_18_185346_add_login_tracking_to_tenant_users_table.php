<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        if (!Schema::connection('tenant')->hasColumn('users', 'last_login_at')) {
            Schema::connection('tenant')->table('users', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable();
            });
        }

        if (!Schema::connection('tenant')->hasColumn('users', 'last_login_ip')) {
            Schema::connection('tenant')->table('users', function (Blueprint $table) {
                $table->string('last_login_ip', 45)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::connection('tenant')->table('users', function (Blueprint $table) {
            if (Schema::connection('tenant')->hasColumn('users', 'last_login_ip')) {
                $table->dropColumn('last_login_ip');
            }

            if (Schema::connection('tenant')->hasColumn('users', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
        });
    }
};
