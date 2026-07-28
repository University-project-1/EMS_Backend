<?php

namespace App\DTOs\Mobile;

class ReportDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?int $event_id,
        public readonly ?int $booth_id,
        public readonly string $title,
        public readonly ?string $description,
    ){}

    public static function formRequest(array $data): self
    {
        return new self(
            event_id: $data['event_id'] ?? null,
            booth_id: $data['booth_id'] ?? null,
            title: $data['title'],
            description: $data['description'] ?? null
        );
    }
}
