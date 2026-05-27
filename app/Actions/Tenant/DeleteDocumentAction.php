<?php

namespace App\Actions\Tenant;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DeleteDocumentAction
{
    public function execute(Document $document): void
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();
    }
}
