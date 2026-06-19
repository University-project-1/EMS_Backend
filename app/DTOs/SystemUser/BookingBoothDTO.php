<?php

namespace App\DTOs\SystemUser;

class BookingBoothDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $boothId,
        public readonly ?int $companyId,
        public readonly ?string $reasonForBooking,
        public readonly ?array $services,
    ){}

    public static function fromRequest(array $data){
        return new self(
            boothId: $data['booth_id'],
            companyId: $data['company_id'] ?? null,
            reasonForBooking: $data['reason_for_booking'] ?? null,
            services: $data['services'] ?? null,
        );
    }
}
