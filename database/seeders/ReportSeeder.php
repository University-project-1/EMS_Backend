<?php

namespace Database\Seeders;

use App\Enum\ReportStatus;
use App\Enum\Status;
use App\Enum\SystemUserType;
use App\Models\Booth;
use App\Models\Event;
use App\Models\Report;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $events = Event::where('status', Status::APPROVED)->get();

        $booths = Booth::query()
            ->whereNotNull('company_id')
            ->whereHas('boothRequests', function ($query): void {
                $query
                    ->where('status', Status::APPROVED->value)
                    ->whereColumn('booth_requests.company_id', 'booths.company_id');
            })
            ->get();

        $admins = SystemUser::query()
            ->where('type', SystemUserType::ADMIN)
            ->get();

        if ($users->isEmpty() || ($events->isEmpty() && $booths->isEmpty())) {
            return;
        }

        $reportables = $events
            ->concat($booths)
            ->shuffle();

        foreach ($reportables as $reportable) {

            if (random_int(1, 100) > 60) {
                continue;
            }

            $reportsCount = random_int(
                1,
                min(3, $users->count())
            );

            $reporters = $users->random($reportsCount);

            foreach ($reporters as $reporter) {

                $status = fake()->randomElement([
                    ReportStatus::PENDING,
                    ReportStatus::RESOLVED,
                    ReportStatus::REJECTED,
                ]);

                $resolvedBy = null;

                if (
                    $status !== ReportStatus::PENDING &&
                    $admins->isNotEmpty()
                ) {
                    $resolvedBy = $admins->random()->getKey();
                }

                $titles = [
                    'Incorrect information',
                    'Missing information',
                    'Invalid event details',
                    'Incorrect booth information',
                    'Problem with the displayed information',
                    'Content needs clarification',
                    'Incorrect location',
                    'Outdated information',
                ];

                $title = fake()->randomElement($titles);

                Report::query()->firstOrCreate(
                    [
                        'reporter_type' => $reporter::class,
                        'reporter_id' => $reporter->getKey(),
                        'reportable_type' => $reportable::class,
                        'reportable_id' => $reportable->getKey(),
                        'title' => $title,
                    ],
                    [
                        'description' => fake()->paragraph(
                            random_int(1, 2)
                        ),
                        'status' => $status,
                        'resolved_by' => $resolvedBy,
                    ],
                );
            }
        }
    }
}
