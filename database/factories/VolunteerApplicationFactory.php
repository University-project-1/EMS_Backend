<?php

namespace Database\Factories;

use App\Enum\Status;
use App\Models\VolunteerApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<VolunteerApplication> */
class VolunteerApplicationFactory extends Factory
{
    protected $model = VolunteerApplication::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+963'.fake()->numerify('9########'),
            'motivation' => fake()->paragraph(),
            'education_or_occupation' => fake()->jobTitle(),
            'skills' => fake()->sentence(),
            'city' => fake()->city(),
            'privacy_consent_at' => now(),
            'status' => Status::PENDING,
        ];
    }
}
