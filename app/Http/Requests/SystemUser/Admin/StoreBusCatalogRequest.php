<?php

namespace App\Http\Requests\SystemUser\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Log;

class StoreBusCatalogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Log::info(00);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     */
    public function rules(): array
    {
        Log::info(3);
        return [
            'location'   => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'duration'   => ['required', 'integer', 'min:1'],
        ];
    }
}
