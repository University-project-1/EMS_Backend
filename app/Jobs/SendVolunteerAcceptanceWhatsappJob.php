<?php

namespace App\Jobs;

use App\Enum\Status;
use App\Models\VolunteerApplication;
use App\Services\Shared\UltraMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendVolunteerAcceptanceWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(public readonly int $volunteerApplicationId) {}

    public function handle(UltraMessageService $ultraMessage): void
    {
        $application = VolunteerApplication::query()->findOrFail($this->volunteerApplicationId);

        if ($application->status !== Status::APPROVED || $application->whatsapp_notification_sent_at) {
            return;
        }

        $ultraMessage->sendChatMessage(
            $application->phone,
            __('volunteer.whatsapp.acceptance', [
                'name' => $application->full_name,
                'group_url' => config('volunteer.whatsapp_group_url'),
            ], 'ar')
        );

        $application->updateQuietly([
            'whatsapp_notification_sent_at' => now(),
            'whatsapp_notification_failed_at' => null,
            'whatsapp_notification_error' => null,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        VolunteerApplication::query()
            ->whereKey($this->volunteerApplicationId)
            ->update([
                'whatsapp_notification_failed_at' => now(),
                'whatsapp_notification_error' => mb_substr($exception->getMessage(), 0, 2000),
            ]);

        Log::error('Volunteer acceptance WhatsApp delivery failed.', [
            'volunteer_application_id' => $this->volunteerApplicationId,
            'exception' => $exception::class,
        ]);
    }
}
