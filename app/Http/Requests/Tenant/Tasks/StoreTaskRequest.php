<?php

namespace App\Http\Requests\Tenant\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('tenant')->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'users' => ['required', 'array', 'min:1'],
            'users.*' => ['exists:tenant.users,id'],
        ];
    }
}
