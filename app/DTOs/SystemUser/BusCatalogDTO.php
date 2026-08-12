<?php

namespace App\DTOs\SystemUser;

use App\Trait\HasSnakeCaseArray;

class BusCatalogDTO
{
    use HasSnakeCaseArray;
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $location,
        public readonly string $startTime,
        public readonly string $endTime,
        public readonly int $duration
    ){}

    public static function fromRequest(array $data) : self {
        return new self(
            location: $data['location'],
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            duration: $data['duration']
        );
    }
}
