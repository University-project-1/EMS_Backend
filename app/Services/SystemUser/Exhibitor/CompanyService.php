<?php

namespace App\Services\SystemUser\Exhibitor;

use App\DTOs\SystemUser\CompanyDTO;
use App\Models\Company;
use App\Models\SystemUser;

class CompanyService
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    public function create(SystemUser $user, CompanyDTO $dto)
    {
        $company = Company::create($dto->toArray());

        $user->companies()->attach($company->id, [
            'created_at' => now(),
        ]);

        if ($dto->logo) {
            $company->addMedia($dto->logo)->toMediaCollection('logo');
        }

        if (! empty($dto->gallery)) {
            foreach ($dto->gallery as $image) {
                $company->addMedia($image)->toMediaCollection('gallery');
            }
        }

        return $company;
    }
}
