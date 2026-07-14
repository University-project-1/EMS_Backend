<?php

namespace App\Rules;

use App\Models\Event;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use App\Enum\Status;
use Illuminate\Support\Carbon;

class EventHallAvailable implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */

    public function __construct(
        protected string $start_at,
        protected int $duration
    ) {}


    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = Carbon::parse($this->start_at);
        
        $end = $start->copy()->addHours($this->duration);

        $exists = Event::query()
            ->where('event_hall_id', $value)
            ->where('status', Status::APPROVED)
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->exists();

        if ($exists) {
            $fail(__('event.hall_unavailable'));
        }
    }
}
