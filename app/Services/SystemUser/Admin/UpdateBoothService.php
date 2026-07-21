<?php

namespace App\Services\SystemUser\Admin;

use App\DTOs\SystemUser\BoothUpdateDTO;
use App\Models\Booth;
use Http\Discovery\Exception\NotFoundException;

class UpdateBoothService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function update(Booth $booth, BoothUpdateDTO $dto){
        if(!$booth){
            throw new NotFoundException();
        }
        $updatedData = $dto->updatePayload();
        $booth->update($updatedData);

        return $booth->refresh();
    }
}
