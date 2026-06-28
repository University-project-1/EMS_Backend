<?php

namespace App\Http\Requests\SystemUser\Exhibitor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterExhibitorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('system_users', 'email')->whereNotNull('email_verified_at')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invite_token' => ['nullable', 'string', 'size:20'],
        ];
    }
}
