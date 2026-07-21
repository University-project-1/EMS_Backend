<?php

namespace App\Services\SystemUser\Admin;

use App\DTOs\SystemUser\AnnouncementDTO;
use App\DTOs\SystemUser\UpdateAnnouncementDTO;
use App\Models\Announcement;
use Illuminate\Support\Facades\Log;

class AnnouncementService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function create(AnnouncementDTO $dto){
        $announcement = Announcement::create($dto->toArray());
        if($dto->media) $announcement->addMedia($dto->media)->toMediaCollection('announcement');
        return $announcement;
    }

    public function edit(Announcement $announcement, UpdateAnnouncementDTO $dto){
        $updatedData = $dto->updatePayload();
        $announcement->update($updatedData);
        return $announcement;
    }
}
