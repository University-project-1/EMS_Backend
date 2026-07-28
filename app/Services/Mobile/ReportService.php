<?php

namespace App\Services\Mobile;

use App\DTOs\Mobile\ReportDTO;
use App\Models\Booth;
use App\Models\Event;
use Illuminate\Contracts\Auth\Authenticatable;

class ReportService
{
    public function store(ReportDTO $dto, Authenticatable $user){
        $reportable_type = null;
        $reportable_id = null;
        if($dto->event_id){
            $reportable_type = Event::class;
            $reportable_id = $dto->event_id;
        }elseif($dto->booth_id){
            $reportable_type = Booth::class;
            $reportable_id = $dto->booth_id;
        }

        $user->reports()->create([
            'reportable_type' => $reportable_type,
            'reportable_id' => $reportable_id,
            'title' => $dto->title,
            'description' => $dto->description,
        ]);
    }
}
