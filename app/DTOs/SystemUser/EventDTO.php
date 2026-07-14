<?php

namespace App\DTOs\SystemUser;

class EventDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $eventHallId,
        public readonly ?int $companyId,
        public readonly string $type,
        public readonly string $title,
        public readonly string $description,
        public readonly string $start_at,
        public readonly int $duration,
        public readonly array $speakers,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            eventHallId: $data['event_hall_id'],
            companyId: $data['company_id'] ?? null,
            type: $data['type'],
            title: $data['title'],
            description: $data['description'],
            start_at: $data['start_at'],
            duration: $data['duration'],
            speakers: $data['speakers'],
        );
    }
}
