<?php

namespace App\Actions\Tenant;

use App\Http\Requests\Tenant\Documents\StoreDocumentRequest;
use App\Models\Document;
use App\Models\TenantUser;
use App\Support\Tenant\TenantUserContext;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UploadDocumentAction
{
    public function __construct(
        private readonly TenantUserContext $tenantUserContext,
    ) {
    }

    public function execute(StoreDocumentRequest $request, TenantUser $user, string $tenant): Document
    {
        $visibility = $request->string('visibility')->toString();

        if ($user->isEmployee() && $visibility !== 'private') {
            abort(403);
        }

        if ($user->isManager() && !in_array($visibility, ['private', 'team'], true)) {
            abort(403);
        }

        if ($user->isAdmin() && !in_array($visibility, ['private', 'org'], true)) {
            abort(403);
        }

        $teamId = null;

        if ($visibility === 'team') {
            $teamId = $this->tenantUserContext->managedTeamId($user);

            if (!$teamId) {
                throw ValidationException::withMessages([
                    'visibility' => 'Manager must be assigned to exactly one team.',
                ]);
            }
        }

        $file = $request->file('file');
        $path = $file->store("tenants/{$tenant}/documents");

        $document = Document::query()->create([
            'uploaded_by' => $user->id,
            'title' => $request->string('title')->toString(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'visibility' => $visibility,
            'team_id' => $teamId,
            'assigned_user_id' => $visibility === 'private' ? $user->id : null,
        ]);

        activity_log(
            'document_uploaded',
            "has uploaded document '{$document->title}'",
            $document
        );

        return $document;
    }
}
