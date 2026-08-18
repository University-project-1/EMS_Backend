<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Propaganistas\LaravelPhone\Rules\Phone;

class StoreVolunteerApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('volunteer_applications', 'email')],
            'phone' => ['required', 'string', 'max:20', new Phone, Rule::unique('volunteer_applications', 'phone')],
            'cv' => ['required', File::types(['pdf', 'doc', 'docx'])->max((int) config('volunteer.cv_max_kilobytes'))],
            'motivation' => ['required', 'string', 'min:20', 'max:3000'],
            'education_or_occupation' => ['required', 'string', 'min:3', 'max:1000'],
            'skills' => ['nullable', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:255'],
            'privacy_consent' => ['accepted'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    protected function prepareForValidation(): void
    {

        $this->merge([
            'full_name' => $this->stringValue('full_name'),
            'email' => $this->filled('email') ? mb_strtolower(trim((string) $this->input('email'))) : null,
            'phone' => $this->stringValue('phone'),
            'motivation' => $this->stringValue('motivation'),
            'education_or_occupation' => $this->stringValue('education_or_occupation'),
            'skills' => $this->stringValue('skills'),
            'city' => $this->stringValue('city'),
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        /** @var array<string, string> $messages */
        $messages = __('volunteer.validation');

        return $messages;
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        /** @var array<string, string> $attributes */
        $attributes = __('volunteer.attributes');

        return $attributes;
    }

    private function stringValue(string $key): ?string
    {
        return $this->filled($key) ? trim((string) $this->input($key)) : null;
    }
}
