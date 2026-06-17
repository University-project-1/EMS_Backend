<?php

namespace App\DTOs\SystemUser;

use App\DTOs\PatchDTO;
use App\Trait\HasUpdatePayload;

class UpdateServiceDTO extends PatchDTO
{
    use HasUpdatePayload;
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
            name: $data['name'],
            price: $data['price'],
            isActive: $data['is_active'],
            payload: $data,
        );
    }
}
