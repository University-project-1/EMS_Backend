<?php

namespace App\DTOs\SystemUser;

use App\DTOs\PatchDTO;
use App\Trait\HasUpdatePayload;

class BoothUpdateDTO extends PatchDTO
{

    use HasUpdatePayload;
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?string $number,
        public readonly ?string $svgId,
        public readonly ?string $price,
        public readonly ?string $area,
        public readonly ?string $qrToken,
        array $payload
    ){
        parent::__construct($payload);
    }

    public static function fromRequest(array $data){
        return new self(
            number: $data['number'] ?? null,
            svgId: $data['svg_id'] ?? null,
            price: $data['price'] ?? null,
            area: $data['area'] ?? null,
            qrToken: $data['qr_token'] ?? null,
            payload: $data,
        );
    }
}
