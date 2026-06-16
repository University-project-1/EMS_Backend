<?php

namespace App\DTOs;

abstract class PatchDTO
{
    public function __construct(
        protected array $payload
    ) {}

    public function payload(): array
    {
        return $this->payload;
    }

    public function has(string $field): bool
    {
        return array_key_exists(
            $field,
            $this->payload
        );
    }
}
