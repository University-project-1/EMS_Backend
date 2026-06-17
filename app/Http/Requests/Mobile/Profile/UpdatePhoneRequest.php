<?php

namespace App\Http\Requests\Mobile\Profile;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;

class UpdatePhoneRequest extends FormRequest
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
            'phone' => ['required', 'string','max:20', Rule::unique('users', 'phone'),new Phone()],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => __('profile.phone_already_exists'),
        ];
    }
}
