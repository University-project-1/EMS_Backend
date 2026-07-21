<?php

namespace App\Rules;

use App\Enum\Status;
use App\Models\Event;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Illuminate\Translation\PotentiallyTranslatedString;
use Throwable;

class EventHallAvailable implements ValidationRule
{
    public function __construct(
        protected mixed $startAt,
        protected mixed $duration,
    ) {}

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($this->startAt) || ! is_numeric($this->duration)) {
            return;
        }

        try {
            $start = Carbon::parse($this->startAt);
        } catch (Throwable) {
            return;
        }

        $end = $start->copy()->addHours((int) $this->duration);

        $exists = Event::query()
            ->where('event_hall_id', $value)
            ->where('status', Status::APPROVED->value)
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start)
            ->exists();

        if ($exists) {
            $fail(__('event.hall_unavailable'));
        }
    }
}
