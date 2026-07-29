<?php

namespace App\Http\Requests\Mobile;

use App\Enum\Status;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreReviewRequest extends FormRequest
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
            'event_id' => ['required_without:booth_id', 'prohibits:booth_id', 'integer', 
                Rule::exists('events', 'id')->where(function ($query) {
                    $query->where('status', Status::APPROVED);
                })
            ],
            'booth_id' => ['required_without:event_id', 'prohibits:event_id', 'integer', 
                Rule::exists('booths', 'id')->where(function ($query) {
                        $query->where('company_id', '!=', null);
                })
            ],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
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
