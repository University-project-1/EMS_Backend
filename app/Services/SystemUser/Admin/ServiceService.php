<?php

namespace App\Services\SystemUser\Admin;

use App\DTOs\SystemUser\ServiceDTO;
use App\DTOs\SystemUser\UpdateServiceDTO;
use App\Models\Service;

class ServiceService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function create(ServiceDTO $dto){
        $service = Service::create(get_object_vars($dto));
        return $service;
    }

    public function update(Service $service, UpdateServiceDTO $dto){
        $updatedData = $dto->updatePayload();
        $service->update($updatedData);

        return $service;
    }
}
