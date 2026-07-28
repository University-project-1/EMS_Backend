<?php

namespace App\Http\Requests\Mobile;

use App\Enum\Status;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
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
            'event_id' => ['nullable', 'prohibits:booth_id', 'integer', 
                Rule::exists('events', 'id')->where(function ($query) {
                    $query->where('status', Status::APPROVED);
                })
            ],
            'booth_id' => ['nullable', 'prohibits:event_id', 'integer', 
                Rule::exists('booths', 'id')->where(function ($query) {
                        $query->where('company_id', '!=', null);
                })
            ],
            'title' => ['required', 'string'],
            'description' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            
        'event_id.exists' => __('validation.event_id.exists'),
        'booth_id.exists' => __('validation.booth_id.exists'),
        ];
    }
}
