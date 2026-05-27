<?php

namespace App\Http\Requests\Tenant\Documents;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('tenant')->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:5120', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg'],
            'visibility' => ['required', 'in:private,team,org'],
        ];
    }
}
