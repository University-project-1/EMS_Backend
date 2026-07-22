<?php

namespace App\Http\Requests\Mobile;

use App\Enum\EventType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexEventRequest extends FormRequest
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
            'filter' => ['sometimes', 'array:date,type,saved'],
            'filter.date' => ['sometimes', 'date_format:Y-m-d'],
            'filter.type' => ['sometimes', Rule::enum(EventType::class)],
            'filter.saved' => ['sometimes', Rule::in(['true', 'false', '1', '0', 1, 0, true, false])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
