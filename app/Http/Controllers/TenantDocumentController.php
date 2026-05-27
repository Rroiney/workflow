<?php

namespace App\Http\Controllers;

use App\Actions\Tenant\DeleteDocumentAction;
use App\Actions\Tenant\UploadDocumentAction;
use App\Http\Requests\Tenant\Documents\StoreDocumentRequest;
use App\Models\Document;
use App\Models\Team;
use App\Repositories\Tenant\DocumentRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TenantDocumentController extends Controller
{
    public function index(DocumentRepository $documentRepository): View
    {
        $this->authorize('viewAny', Document::class);

        $documents = $documentRepository->getIndexDocuments(auth('tenant')->user());
        return view('tenant.documents.index', compact('documents'));
    }

    public function create(): View
    {
        $this->authorize('create', Document::class);

        $teams = Team::all();

        return view('tenant.documents.create', compact('teams'));
    }

    public function store(
        StoreDocumentRequest $request,
        UploadDocumentAction $uploadDocumentAction,
        string $tenant
    )
    {
        $user = auth('tenant')->user();
        $this->authorize('create', Document::class);

        $uploadDocumentAction->execute($request, $user, $tenant);

        return redirect()
            ->route('documents.index', ['tenant' => $tenant])
            ->with('success', 'Document uploaded successfully.');
    }

    public function download(string $tenant, Document $document): BinaryFileResponse
    {
        $this->authorize('view', $document);

        return response()->download(
            Storage::disk('local')->path($document->file_path),
            $document->file_name
        );
    }

    public function preview(string $tenant, Document $document)
    {
        $this->authorize('view', $document);

        $mime = $document->mime_type;
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

    public function destroy(
        string $tenant,
        Document $document,
        DeleteDocumentAction $deleteDocumentAction
    )
    {
        $this->authorize('delete', $document);

        $deleteDocumentAction->execute($document);

        return back()->with('success', 'Document deleted successfully.');
    }
}
