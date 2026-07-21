<?php

namespace App\Http\Requests\SystemUser\Exhibitor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EventCalendarRequest extends FormRequest
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
            'event_hall_id' => ['required', 'integer', 'exists:event_halls,id'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after:from'],
        ];
    }
}
