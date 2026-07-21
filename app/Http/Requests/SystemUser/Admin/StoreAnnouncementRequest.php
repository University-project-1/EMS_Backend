<?php

namespace App\Http\Requests\SystemUser\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2048'],
            'receiver' => ['required', 'string', 'in:Exhibitors,visitors,all'],
            'is_active' => ['sometimes','nullable', 'boolean'],
            'media' => ['sometimes', 'nullable', 'mimes:png,jpg,jpeg,webg,pdf', 'max:8192']
        ];
    }
}
