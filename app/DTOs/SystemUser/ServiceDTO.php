<?php

namespace App\DTOs\SystemUser;

class ServiceDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly float $price,
    ){}

    public static function fromRequest(array $data){
        return new self(
            name: $data['name'],
            price: $data['price'],
        );
    }
}
