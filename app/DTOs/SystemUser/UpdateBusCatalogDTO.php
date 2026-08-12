<?php

namespace App\DTOs\SystemUser;

use App\DTOs\PatchDTO;

class UpdateBusCatalogDTO extends PatchDTO{
    public function __construct(
        public readonly ?string $location,
        public readonly ?string $startTime,
        public readonly ?string $endTime,
        public readonly ?int $duration,
        array $payload
    ){
        parent::__construct($payload);
    }

    public static function fromRequest(array $data) : self{
        return new self(
            location: $data['location'] ?? null,
            startTime: $data['start_time'] ?? null,
            endTime: $data['end_time'] ?? null,
            duration: $data['duration'] ?? null,
            payload: $data
        );
    }
}
