<?php

namespace Tests\Feature\Tenant;

use Tests\TestCase;

class TenantAuthTest extends TestCase
{
    public function test_tenant_user_can_log_in_and_login_metadata_is_updated(): void
    {
        $user = $this->createTenantUser('admin', [
            'email' => 'admin@example.test',
        ]);

        $response = $this->post($this->tenantUrl('/login'), [
            'email' => 'admin@example.test',
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertRedirect(route('home', ['tenant' => $this->tenant->slug], false));
        $this->assertAuthenticatedAs($user, 'tenant');
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_tenant_login_rejects_invalid_credentials(): void
    {
        $this->createTenantUser('employee', [
            'email' => 'employee@example.test',
        ]);

        $response = $this->from($this->tenantUrl('/login'))->post($this->tenantUrl('/login'), [
            'email' => 'employee@example.test',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect($this->tenantUrl('/login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('tenant');
    }
}
