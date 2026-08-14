<?php

namespace App\Observers;

use App\Enum\LeadInterestNotificationType;
use App\Models\Booth;
use App\Models\Company;
use App\Services\Mobile\LeadInterestAudienceResolver;
use App\Services\Mobile\LeadInterestNotificationDispatcher;
use Illuminate\Support\Facades\DB;

class BoothObserver
{
    public function __construct(
        private readonly LeadInterestAudienceResolver $audienceResolver,
        private readonly LeadInterestNotificationDispatcher $notificationDispatcher,
    ) {}

    public function updated(Booth $booth): void
    {
        if (! $booth->wasChanged('company_id') || $booth->company_id === null) {
            return;
        }

        DB::afterCommit(function () use ($booth): void {
            $booth->loadMissing('company');

            if (! $booth->company instanceof Company) {
                return;
            }

            $this->notificationDispatcher->send(
                $this->audienceResolver->forCompany($booth->company),
                $booth,
                LeadInterestNotificationType::COMPANY_BOOTH_CREATED,
            );
        });
    }
}
