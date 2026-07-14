<?php

namespace App\Http\Requests\SystemUser\Exhibitor;

use App\Enum\EventType;
use App\Http\Requests\SystemUser\Shared\CompanyRules;
use App\Rules\EventHallAvailable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $eventRules = [
            'event_hall_id' => ['required','integer','exists:event_halls,id',new EventHallAvailable($this->input('start_at'),$this->input('duration'),)],
            'company_id' => ['required_without:new_company','prohibits:new_company','nullable','integer','exists:companies,id',],
            'new_company' => ['required_without:company_id','prohibits:company_id','nullable','array',],
            'type' => ['required',Rule::enum(EventType::class),],
            'title' => ['required','string','max:255',],
            'description' => ['required','string','max:5000',],
            'start_at' => ['required','date','after:now',],
            'duration' => ['required','integer','min:1','max:4',],
            'speakers' => ['required','array','min:1','max:20',],
            'speakers.*.name' => ['required','string','max:255'],
        ];

        $companyRules = CompanyRules::get('new_company');

        return array_merge($eventRules, $companyRules);
    }
}
