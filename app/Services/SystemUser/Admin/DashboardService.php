<?php
namespace App\Services\SystemUser\Admin;

use App\Models\{Booth, BoothRequest, Company, Event, Lead, Report, User};
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\{Cache, DB};

class DashboardService
{
    public function getDashboard(int $days = 7): array
    {
        $days = min(max($days, 1), 31);
        $end = CarbonImmutable::now();
        $start = $end->startOfDay()->subDays($days - 1);
        $cacheKey = "admin-dashboard:{$start->toDateString()}:{$days}";

        return Cache::remember($cacheKey, now()->addSeconds(45), fn () => $this->buildDashboard($start, $end, $days));
    }

    private function buildDashboard(CarbonImmutable $start, CarbonImmutable $end, int $days): array
    {
        return [
            'period' => $this->period($start, $end, $days),
            'summary' => $this->summary($start, $end),
            'trends' => $this->trends($start, $days),
            'breakdowns' => $this->breakdowns(),
        ];
    }

    private function period(CarbonImmutable $start, CarbonImmutable $end, int $days): array
    {
        return ['days' => $days, 'start' => $start->toIso8601String(), 'end' => $end->toIso8601String(), 'generated_at' => CarbonImmutable::now()->toIso8601String(), 'timezone' => config('app.timezone')];
    }

    private function summary(CarbonImmutable $start, CarbonImmutable $end): array
    {
        return ['visitors' => $this->counts(User::query(), $start, $end), 'companies' => $this->counts(Company::query(), $start, $end), 'booths' => $this->boothSummary(), 'pending_booth_requests' => BoothRequest::query()->where('status', 'pending')->count(), 'upcoming_events_30_days' => $this->upcomingEvents(), 'open_reports' => Report::query()->where('status', '!=', 'resolved')->count(), 'leads' => $this->counts(Lead::query(), $start, $end)];
    }

    private function counts($query, CarbonImmutable $start, CarbonImmutable $end): array
    {
        return ['total' => (clone $query)->count(), 'period' => (clone $query)->whereBetween('created_at', [$start, $end])->count()];
    }

    private function boothSummary(): array
    {
        $query = Booth::query();
        return ['total' => (clone $query)->count(), 'allocated' => (clone $query)->whereNotNull('company_id')->count(), 'available' => (clone $query)->whereNull('company_id')->count()];
    }

    private function upcomingEvents(): int
    {
        $now = CarbonImmutable::now();
        return Event::query()->whereBetween('start_at', [$now, $now->addDays(30)])->count();
    }

    private function trends(CarbonImmutable $start, int $days): array
    {
        return ['visitors' => $this->dailySeries(new User(), $start, $days), 'companies' => $this->dailySeries(new Company(), $start, $days), 'booth_requests' => $this->dailySeries(new BoothRequest(), $start, $days), 'leads' => $this->dailySeries(new Lead(), $start, $days), 'events' => $this->dailySeries(new Event(), $start, $days)];
    }

    private function dailySeries($model, CarbonImmutable $start, int $days): array
    {
        $rows = $model->newQuery()->selectRaw('DATE(created_at) AS date, COUNT(*) AS value')->whereBetween('created_at', [$start, $start->addDays($days - 1)->endOfDay()])->groupByRaw('DATE(created_at)')->pluck('value', 'date');
        return collect(range(0, $days - 1))->map(fn (int $offset) => ['date' => $start->addDays($offset)->toDateString(), 'value' => (int) ($rows[$start->addDays($offset)->toDateString()] ?? 0)])->all();
    }

    private function breakdowns(): array
    {
        return ['visitor_gender' => $this->groupedCount(User::query(), 'gender'), 'booth_status' => $this->boothAllocationBreakdown(), 'request_status' => $this->groupedCount(BoothRequest::query(), 'status')];
    }

    private function boothAllocationBreakdown(): array
    {
        $query = Booth::query();

        return [
            'allocated' => (clone $query)->whereNotNull('company_id')->count(),
            'available' => (clone $query)->whereNull('company_id')->count(),
        ];
    }

    private function groupedCount($query, string $column): array
    {
        return $query
            ->select($column, DB::raw('COUNT(*) AS total'))
            ->whereNotNull($column)
            ->groupBy($column)
            ->pluck('total', $column)
            ->map(fn ($value): int => (int) $value)
            ->all();
    }
}
