<?php

namespace App\Services\SystemUser\Admin;

use App\DTOs\SystemUser\UpdateEventHallDTO;
use App\Models\EventHall;
use Http\Discovery\Exception\NotFoundException;

class EventHallService
{
    public function update(EventHall $eventHall, UpdateEventHallDTO $dto){
        if(!$eventHall){
            throw new NotFoundException();
        }

        $updatedData = $dto->updatePayload();
        $eventHall->update($updatedData);

        return $eventHall->refresh();
    }
}
