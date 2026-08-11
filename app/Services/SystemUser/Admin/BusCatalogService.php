<?php

namespace App\Services\SystemUser\Admin;

use App\DTOs\SystemUser\BusCatalogDTO;
use App\DTOs\SystemUser\UpdateBusCatalogDTO;
use App\Models\BusCatalog;

class BusCatalogService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function create(BusCatalogDTO $dto){
        $bus = BusCatalog::create($dto->toArray());
        return $bus;
    }

    public function update(BusCatalog $bus, UpdateBusCatalogDTO $dto){
        $updatedData = $dto->updatePayload();
        $bus->update($updatedData);

        return $bus;
    }
}
