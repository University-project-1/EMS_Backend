<?php

namespace App\Http\Requests\SystemUser\Admin;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusCatalogRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'location'   => ['sometimes', 'string', 'max:255'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time'   => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'duration'   => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
