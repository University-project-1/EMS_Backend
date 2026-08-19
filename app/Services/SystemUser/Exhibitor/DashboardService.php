<?php

namespace App\Services\SystemUser\Exhibitor;

use App\Enum\Status;
use App\Models\Booth;
use App\Models\BoothRequest;
use App\Models\BoothRequestService;
use App\Models\Event;
use App\Models\SystemUser;

class DashboardService
{
    /**
     * @return array{
     *     leads_count: int,
     *     recent_qr_scans_count: int,
     *     services_count: int,
     *     services_total_price: float,
     *     booth_members_count: int,
     *     pending_invitations_count: int,
     *     events_count: int,
     *     approved_events_count: int
     * }
     */
    public function boothStatistics(Booth $booth, SystemUser $systemUser): array
    {
        $boothsTable = $booth->getTable();
        $boothRequestsTable = (new BoothRequest)->getTable();
        $boothRequestServicesTable = (new BoothRequestService)->getTable();

        $approvedRequestServices = BoothRequest::query()
            ->join(
                $boothRequestServicesTable,
                "{$boothRequestServicesTable}.request_id",
                '=',
                "{$boothRequestsTable}.id",
            )
            ->whereColumn("{$boothRequestsTable}.booth_id", "{$boothsTable}.id")
            ->where("{$boothRequestsTable}.status", Status::APPROVED->value);

        $statistics = Booth::query()
            ->whereKey($booth)
            ->select("{$boothsTable}.id")
            ->withCount([
                'leads',
                'leads as recent_qr_scans_count' => fn ($query) => $query
                    ->where('created_at', '>=', now()->subDays(1)),
                'systemUsers as booth_members_count',
                'invitations as pending_invitations_count' => fn ($query) => $query
                    ->where('status', Status::PENDING->value),
            ])
            ->selectSub(
                (clone $approvedRequestServices)->selectRaw('COUNT(*)'),
                'services_count',
            )
            ->selectSub(
                (clone $approvedRequestServices)->selectRaw(
                    "COALESCE(SUM({$boothRequestServicesTable}.quantity * {$boothRequestServicesTable}.unit_price), 0)"
                ),
                'services_total_price',
            )
            ->firstOrFail();

        $eventStatistics = Event::query()
            ->accessibleBy($systemUser)
            ->toBase()
            ->selectRaw('COUNT(*) as events_count')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN status = ? THEN 1 ELSE 0 END), 0) as approved_events_count',
                [Status::APPROVED->value],
            )
            ->first();

        return [
            'leads_count' => (int) $statistics->getAttribute('leads_count'),
            'recent_qr_scans_count' => (int) $statistics->getAttribute('recent_qr_scans_count'),
            'services_count' => (int) $statistics->getAttribute('services_count'),
            'services_total_price' => (float) $statistics->getAttribute('services_total_price'),
            'booth_members_count' => (int) $statistics->getAttribute('booth_members_count'),
            'pending_invitations_count' => (int) $statistics->getAttribute('pending_invitations_count'),
            'events_count' => (int) $eventStatistics->events_count,
            'approved_events_count' => (int) $eventStatistics->approved_events_count,
        ];
    }
}
