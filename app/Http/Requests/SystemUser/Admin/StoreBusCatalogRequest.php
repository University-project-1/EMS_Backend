<?php

namespace App\Http\Requests\SystemUser\Admin;
use Illuminate\Foundation\Http\FormRequest;

class StoreBusCatalogRequest extends FormRequest
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
            'location'   => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'duration'   => ['required', 'integer', 'min:1'],
        ];
    }
}
