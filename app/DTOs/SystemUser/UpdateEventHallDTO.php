<?php

namespace App\DTOs\SystemUser;

use App\DTOs\PatchDTO;

class UpdateEventHallDTO extends PatchDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?float $price_per_hour,
        array $payload,
    ){
        parent::__construct($payload);
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            price_per_hour: $data['price_per_hour'] ?? null,
            payload: $data,
        );
    }
}
