<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class TenantsMigrate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenants:migrate';

    /**
     * The console command description.
     */
    protected $description = 'Run tenant migrations for all tenants';

    public function handle()
    {
        $this->info('Starting tenant migrations...');

        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('No tenants found.');
            return Command::SUCCESS;
        }

        foreach ($tenants as $tenant) {

            $this->info("Migrating tenant: {$tenant->slug}");

            // Dynamically set tenant DB connection
            config([
                'database.connections.tenant.database' => $tenant->db_name,
                'database.connections.tenant.username' => $tenant->db_username,
                'database.connections.tenant.password' => $tenant->db_password,
                'database.connections.tenant.host'     => $tenant->db_host,
                'database.connections.tenant.port'     => $tenant->db_port,
            ]);

            DB::purge('tenant');
            DB::reconnect('tenant');

            // Run tenant migrations only
            $this->call('migrate', [
                '--path'     => 'database/migrations/tenant',
                '--database' => 'tenant',
                '--force'    => true,
            ]);
        }

        $this->info('Tenant migrations completed.');

        return Command::SUCCESS;
    }
}
