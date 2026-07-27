<?php

namespace App\Http\Requests\SystemUser\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:2048'],
            'receiver' => ['sometimes', 'string', 'in:Exhibitors,visitors,all'],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
            'media' => ['sometimes', 'nullable', 'file', 'mimes:png,jpg,jpeg,webg,pdf', 'max:8192']

        ];
    }
}
