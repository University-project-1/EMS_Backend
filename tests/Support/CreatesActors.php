<?php

namespace Tests\Support;

use App\Enum\SystemUserType;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CreatesActors
{
    protected function createAdministrator(array $overrides = []): SystemUser
    {
        return $this->createSystemUser(SystemUserType::ADMIN, $overrides);
    }

    protected function createExhibitor(array $overrides = []): SystemUser
    {
        return $this->createSystemUser(SystemUserType::EXHIBITOR, $overrides);
    }

    protected function createVisitor(array $overrides = []): User
    {
        return User::factory()->create($overrides);
    }

    private function createSystemUser(SystemUserType $type, array $overrides = []): SystemUser
    {
        return SystemUser::query()->create(array_merge([
            'name' => Str::title($type->value).' Test User',
            'email' => $type->value.'-'.Str::uuid().'@example.test',
            'password' => Hash::make('password'),
            'type' => $type,
            'email_verified_at' => now(),
        ], $overrides));
    }
}
