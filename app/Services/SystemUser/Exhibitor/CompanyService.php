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
        Log::info('in create comp');
        $company = $user->companies()->create($dto->toArray());
        return $company;
    }
}
