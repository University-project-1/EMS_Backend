<?php

namespace App\DTOs\SystemUser;

use App\DTOs\PatchDTO;

class UpdateServiceDTO extends PatchDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?string $name,
        public readonly ?float $price,
        public readonly ?bool $isActive,
        array $payload,
    ){
        parent::__construct($payload);
    }

    public static function fromRequest(array $data){
        return new self(
            name: $data['name'] ?? null,
            price: $data['price'] ?? null,
            isActive: $data['is_active'] ?? null,
            payload: $data,
        );
    }
}
