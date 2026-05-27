<?php

namespace App\Repositories\Tenant;

use App\Models\Document;
use App\Models\TenantUser;
use App\Support\Tenant\TenantUserContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DocumentRepository
{
    public function __construct(
        private readonly TenantUserContext $tenantUserContext,
    ) {
    }

    public function visibleTo(TenantUser $user): Builder
    {
        $teamIds = $this->tenantUserContext->accessibleTeamIds($user);

        return Document::query()->where(function (Builder $query) use ($user, $teamIds) {
            $query->where(function (Builder $privateQuery) use ($user) {
                $privateQuery->where('visibility', 'private')
                    ->where('uploaded_by', $user->id);
            });

            if ($teamIds !== []) {
                $query->orWhere(function (Builder $teamQuery) use ($teamIds) {
                    $teamQuery->where('visibility', 'team')
                        ->whereIn('team_id', $teamIds);
                });
            }

            $query->orWhere('visibility', 'org');
        });
    }

    public function getIndexDocuments(TenantUser $user): Collection
    {
        return $this->visibleTo($user)->latest()->get();
    }

    public function countVisibleFor(TenantUser $user): int
    {
        return $user->isAdmin()
            ? Document::query()->count()
            : $this->visibleTo($user)->count();
    }
}
