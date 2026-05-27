<?php

namespace Tests\Feature\Tenant;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    public function test_employee_only_sees_documents_they_are_allowed_to_access(): void
    {
        $employee = $this->signInTenantUser($this->createTenantUser('employee'));
        $manager = $this->createTenantUser('manager');
        $team = $this->createTeam($manager, [$employee]);

        $ownPrivate = $this->createDocument($employee, ['title' => 'My Private']);
        $orgDocument = $this->createDocument($manager, [
            'title' => 'Org Document',
            'visibility' => 'org',
            'assigned_user_id' => null,
        ]);
        $teamDocument = $this->createDocument($manager, [
            'title' => 'Team Document',
            'visibility' => 'team',
            'team_id' => $team->id,
            'assigned_user_id' => null,
        ]);
        $hiddenPrivate = $this->createDocument($manager, [
            'title' => 'Hidden Private',
        ]);

        $response = $this->get($this->tenantUrl('/documents'));

        $response->assertOk();
        $response->assertSee($ownPrivate->title);
        $response->assertSee($orgDocument->title);
        $response->assertSee($teamDocument->title);
        $response->assertDontSee($hiddenPrivate->title);
    }

    public function test_employee_cannot_upload_org_document(): void
    {
        Storage::fake('local');
        $employee = $this->signInTenantUser($this->createTenantUser('employee'));

        $response = $this->post($this->tenantUrl('/documents/upload'), [
            'title' => 'Policy',
            'visibility' => 'org',
            'file' => UploadedFile::fake()->create('policy.pdf', 64, 'application/pdf'),
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('documents', 0, 'tenant');
    }

    public function test_manager_can_upload_team_document_and_employee_cannot_download_unrelated_private_document(): void
    {
        Storage::fake('local');
        $manager = $this->signInTenantUser($this->createTenantUser('manager'));
        $employee = $this->createTenantUser('employee');
        $team = $this->createTeam($manager, [$employee]);

        $uploadResponse = $this->post($this->tenantUrl('/documents/upload'), [
            'title' => 'Team Playbook',
            'visibility' => 'team',
            'file' => UploadedFile::fake()->create('playbook.pdf', 64, 'application/pdf'),
        ]);

        $document = \App\Models\Document::query()->where('title', 'Team Playbook')->firstOrFail();
        $uploadResponse->assertRedirect(route('documents.index', ['tenant' => $this->tenant->slug], false));
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'visibility' => 'team',
            'team_id' => $team->id,
        ], 'tenant');

        $outsider = $this->createTenantUser('employee');
        $privateDocument = $this->createDocument($employee, ['visibility' => 'private']);
        $this->actingAs($outsider, 'tenant');

        $downloadResponse = $this->get($this->tenantUrl("/documents/{$privateDocument->id}/download"));
        $downloadResponse->assertForbidden();
    }
}
