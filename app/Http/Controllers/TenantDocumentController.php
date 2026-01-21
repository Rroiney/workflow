<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TenantUser;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class TenantDocumentController extends Controller
{
    public function index()
    {
        $user = auth('tenant')->user();

        /** @var TenantUser $user */
        // Employee teams (via pivot)
        $memberTeamIds = $user->teams()->pluck('teams.id')->toArray();

        // Manager team (one team by design)
        $managedTeamId = \App\Models\Team::where('manager_id', $user->id)->value('id');

        $teamIds = array_unique(array_merge(
            $memberTeamIds,
            $managedTeamId ? [$managedTeamId] : []
        ));

        $documents = \App\Models\Document::where(function ($q) use ($user, $teamIds) {

            // Private
            $q->where(function ($q) use ($user) {
                $q->where('visibility', 'private')
                    ->where('uploaded_by', $user->id);
            });

            // Team
            if (!empty($teamIds)) {
                $q->orWhere(function ($q) use ($teamIds) {
                    $q->where('visibility', 'team')
                        ->whereIn('team_id', $teamIds);
                });
            }

            // Org
            $q->orWhere('visibility', 'org');
        })
            ->latest()
            ->get();

        return view('tenant.documents.index', compact('documents'));
    }

    public function create()
    {
        $teams = Team::all();
        return view('tenant.documents.create', compact('teams'));
    }

    public function store(Request $request)
    {
        $user = auth('tenant')->user();
        $tenant = request()->route('tenant');

        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,png,jpg',
            'visibility' => 'required|in:private,team,org',
        ]);


        // ROLE RULES
        if ($user->role === 'employee' && $request->visibility !== 'private') {
            abort(403);
        }

        if ($user->role === 'manager' && !in_array($request->visibility, ['private', 'team'])) {
            abort(403);
        }

        if ($user->role === 'admin' && !in_array($request->visibility, ['private', 'org'])) {
            abort(403);
        }

        // RESOLVE TEAM STRICTLY
        $teamId = null;

        if ($request->visibility === 'team') {
            $teams = \App\Models\Team::where('manager_id', $user->id)->pluck('id');

            if ($teams->count() !== 1) {
                return back()->withErrors([
                    'visibility' => 'Manager must be assigned to exactly one team.',
                ]);
            }

            $teamId = $teams->first();
        }

        // FILE STORAGE
        $file = $request->file('file');
        $path = $file->store("tenants/{$tenant}/documents");

        // CREATE DOCUMENT
        $document = \App\Models\Document::create([
            'tenant_id' => $tenant,
            'uploaded_by' => $user->id,
            'title' => $request->title,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'visibility' => $request->visibility,
            'team_id' => $teamId,
            'assigned_user_id' => $request->visibility === 'private' ? $user->id : null,
        ]);

        activity_log(
            'document_uploaded',
            "has uploaded document '{$document->title}'",
            $document
        );


        return redirect()
            ->route('documents.index', ['tenant' => $tenant])
            ->with('success', 'Document uploaded successfully.');
    }

    public function download($tenant, $documentId)
    {
        $user = auth('tenant')->user();

        $document = \App\Models\Document::findOrFail($documentId);

        /** @var TenantUser $user */
        // Employee teams
        $memberTeamIds = $user->teams()->pluck('teams.id')->toArray();

        // Manager team
        $managedTeamId = \App\Models\Team::where('manager_id', $user->id)->value('id');

        $teamIds = array_unique(array_merge(
            $memberTeamIds,
            $managedTeamId ? [$managedTeamId] : []
        ));

        // ACCESS CHECK
        if (
            ($document->visibility === 'private' && $document->uploaded_by !== $user->id) ||
            ($document->visibility === 'team' && !in_array($document->team_id, $teamIds))
        ) {
            abort(403);
        }

        return response()->download(
            Storage::disk('local')->path($document->file_path),
            $document->file_name
        );
    }

    public function preview($tenant, $documentId)
    {
        $user = auth('tenant')->user();
        $document = \App\Models\Document::findOrFail($documentId);

        /** ---- ACCESS CHECK (UNCHANGED) ---- */
        /** @var TenantUser $user */
        $memberTeamIds = $user->teams()->pluck('teams.id')->toArray();
        $managedTeamId = \App\Models\Team::where('manager_id', $user->id)->value('id');

        $teamIds = array_unique(array_merge(
            $memberTeamIds,
            $managedTeamId ? [$managedTeamId] : []
        ));

        if (
            ($document->visibility === 'private' && $document->uploaded_by !== $user->id) ||
            ($document->visibility === 'team' && !in_array($document->team_id, $teamIds))
        ) {
            abort(403);
        }

        /** ---- PREVIEW LOGIC (FIXED) ---- */
        $mime = $document->mime_type;

        // IMPORTANT: use local disk (or tenant disk if you have one)
        $disk = Storage::disk('local');

        if (!$disk->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        // Inline preview (image / pdf)
        if (str_starts_with($mime, 'image/') || $mime === 'application/pdf') {
            return response()->file(
                $disk->path($document->file_path),
                [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
                ]
            );
        }

        // Fallback → download
        return response()->download(
            $disk->path($document->file_path),
            $document->file_name
        );
    }

    public function destroy($tenant, $documentId)
    {
        $user = auth('tenant')->user();
        $document = Document::findOrFail($documentId);

        if (!$this->canDelete($user, $document)) {
            abort(403);
        }

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }

    private function canDelete($user, $document): bool
    {
        // Employee → only own private docs
        if ($user->role === 'employee') {
            return $document->visibility === 'private'
                && $document->uploaded_by === $user->id;
        }

        // Manager → own private + team docs
        if ($user->role === 'manager') {
            return in_array($document->visibility, ['private', 'team'])
                && $document->uploaded_by === $user->id;
        }

        // Admin → can delete anything
        return $user->role === 'admin';
    }
}
