<?php

namespace App\Http\Requests\SystemUser\Exhibitor;

use App\Http\Requests\SystemUser\Shared\CompanyRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreBoothRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        $companyId = $this->input('company_id');
        if ($companyId) {
            return DB::table('company_system_users')
                ->where('company_id', $companyId)
                ->where('system_user_id', $this->user('system')->id)
                ->exists();
        }

        return true;
    }

    public function rules(): array
    {
        $boothRules = [
            'booth_id' => ['required', 'integer', 'exists:booths,id', Rule::exists('booths', 'id')->whereNull('company_id')],
            'company_id' => ['required_without:new_company', 'prohibits:new_company', 'nullable', 'integer', 'exists:companies,id'],
            'new_company' => ['required_without:company_id', 'prohibits:company_id', 'nullable', 'array'],

            'reason_for_booking' => ['nullable', 'string', 'max:1000'],
            'products_file' => ['required', 'file', 'mimes:xlsx', 'max:5120'],
            'services' => ['nullable', 'array'],
            'services.*.service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
            'services.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];

        return array_merge($boothRules, CompanyRules::get('new_company'));
    }
}
