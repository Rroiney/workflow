<?php

namespace App\Http\Requests\Tenant\Teams;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('tenant')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'manager_id' => ['required', 'exists:tenant.users,id'],
            'users' => ['nullable', 'array'],
            'users.*' => ['exists:tenant.users,id'],
        ];
    }
}
