<?php

namespace App\DTOs\SystemUser;

use App\Trait\HasSnakeCaseArray;
use Illuminate\Http\UploadedFile;

class CompanyDTO
{
    use HasSnakeCaseArray;

    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $businessSector,
        public readonly array $socialLinks,
        public readonly string $phone,
        public readonly int $yearFounded,
        public readonly string $description,
        public readonly float $headquartersLat,
        public readonly float $headquartersLng,
        public readonly UploadedFile $logo,
        public readonly ?array $gallery,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            businessSector: $data['business_sector'],
            socialLinks: $data['social_links'],
            phone: $data['phone'],
            yearFounded: $data['year_founded'],
            description: $data['description'],
            headquartersLat: $data['headquarters_lat'],
            headquartersLng: $data['headquarters_lng'],
            logo: $data['logo'],
            gallery: $data['gallery'] ?? null,
        );
    }
}
