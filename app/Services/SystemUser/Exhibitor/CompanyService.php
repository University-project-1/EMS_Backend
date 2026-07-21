<?php

namespace App\Services\SystemUser\Exhibitor;

use App\DTOs\SystemUser\CompanyDTO;
use App\Models\SystemUser;
use Illuminate\Support\Facades\Log;

class CompanyService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function create(SystemUser $user, CompanyDTO $dto){
        $company = $user->companies()->create($dto->toArray());
        if($dto->logo){
            $company->addMedia($dto->logo)->toMediaCollection('logo');
        }
        if (!empty($dto->gallery)) {
            foreach ($dto->gallery as $image) {
                $company->addMedia($image)->toMediaCollection('gallery');
            }
        }
        return $company;
    }
}
