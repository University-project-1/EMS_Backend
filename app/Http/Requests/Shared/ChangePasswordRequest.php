<?php
namespace App\Http\Requests\Shared;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can change their active password
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // 'api' specifies the guard to check the current password against
            'current_password' => ['required', 'current_password:system'],
            'new_password'     => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:current_password'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'new_password.different' => 'The new password must be different from the current password.',
        ];
    }
}
