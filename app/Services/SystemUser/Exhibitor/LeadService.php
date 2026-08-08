<?php

namespace App\Services\SystemUser\Exhibitor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeadService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}
    public function getLeadStatistics(Model $leadable): array
    {
        return [
            'leads_count' => $leadable->leads()->count(),
            'visitors' => $this->getLatestVisitors($leadable),
            'weekly_stats' => $this->getWeeklyStats($leadable),
        ];
    }

    private function getLatestVisitors(Model $leadable)
    {
        return $leadable->leads()
            ->with('user:id,first_name,last_name,phone,job')
            ->orderByDesc('created_at')
            ->paginate(5);
    }

    private function getWeeklyStats(Model $leadable): array
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $stats = $leadable->leads()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('count', 'date');

        $formattedStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $formattedStats[] = [
                'date' => $date,
                'day_name' => Carbon::parse($date)->translatedFormat('l'),
                'count' => $stats->get($date, 0),
            ];
        }

        return $formattedStats;
    }
}
