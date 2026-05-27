<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\TenantUser;
use App\Support\Tenant\TenantUserContext;

class DocumentPolicy
{
    public function __construct(
        private readonly TenantUserContext $tenantUserContext,
    ) {
    }

    public function viewAny(TenantUser $user): bool
    {
        return $user !== null;
    }

    public function create(TenantUser $user): bool
    {
        return $user !== null;
    }

    public function view(TenantUser $user, Document $document): bool
    {
        if ($document->visibility === 'org') {
            return true;
        }

        if ($document->visibility === 'private') {
            return $document->uploaded_by === $user->id;
        }

        return in_array(
            $document->team_id,
            $this->tenantUserContext->accessibleTeamIds($user),
            true
        );
    }

    public function delete(TenantUser $user, Document $document): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isEmployee()) {
            return $document->visibility === 'private'
                && $document->uploaded_by === $user->id;
        }

        return in_array($document->visibility, ['private', 'team'], true)
            && $document->uploaded_by === $user->id;
    }
}
