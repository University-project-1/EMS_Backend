<?php

namespace App\DTOs\Mobile;

use App\Enum\Gender;
use Carbon\Carbon;


class RegisterDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $password,
        public readonly string $job,
        public readonly string $location,
        public readonly Carbon $birthday,
        public readonly Gender $gender,
    ) {}

    public static function formRequest(array $data): self
    {
        return new self(
            first_name: $data['first_name'],
            last_name: $data['last_name'],
            email: $data['email'],
            phone: $data['phone'],
            password: $data['password'],
            job: $data['job'],
            location: $data['location'],
            birthday: Carbon::parse($data['birthday']),
            gender: Gender::from($data['gender']),
        );
    }
}