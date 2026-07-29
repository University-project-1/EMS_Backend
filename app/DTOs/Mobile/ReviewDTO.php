<?php

namespace App\DTOs\Mobile;

class ReviewDTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly ?int $event_id,
        public readonly ?int $booth_id,
        public readonly int $rating,
        public readonly ?string $comment,
    ){}

    public static function fromRequest(array $data): self{
        return new self(
            event_id: $data['event_id'] ?? null,
            booth_id: $data['booth_id'] ?? null,
            rating: $data['rating'],
            comment: $data['comment'] ?? null,
        );
    }
}
