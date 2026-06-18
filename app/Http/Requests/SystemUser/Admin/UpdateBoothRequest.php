<?php

namespace App\Http\Requests\SystemUser\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBoothRequest extends FormRequest
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
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number' => ['sometimes', 'string', 'max:255'],
            'area' => ['sometimes', 'numeric'],
            'price' => ['sometimes', 'numeric'],
            'svg_id' => ['sometimes', 'string'],
            'qr_token' => ['sometimes', 'string'],
        ];
    }
}
