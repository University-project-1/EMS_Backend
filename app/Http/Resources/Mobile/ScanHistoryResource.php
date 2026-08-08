<?php
namespace App\Http\Resources\Mobile;

use App\Models\Booth;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScanHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mappedData = match ($this->leadable_type) {
            Booth::class => $this->mapBooth(),
            Event::class => $this->mapEvent(),
            default => $this->mapDefault(),
        };

        return array_merge([
            'id' => $this->id,
            'scanned_at' => $this->created_at,
        ], $mappedData);
    }

    private function mapBooth(): array
    {
        return [
            'type' => 'booth',
            'title' => $this->leadable?->company?->name ?? 'Independent Booth',
            'subtitle' => 'Booth Number: ' . ($this->leadable?->number ?? '-'),
            'image_url' => $this->leadable?->company?->getFirstMediaUrl('logo') ?: null,
        ];
    }

    private function mapEvent(): array
    {
        return [
            'type' => 'event',
            'title' => $this->leadable?->title ?? 'Unknown Event',
            'subtitle' => 'Event - ' . ($this->leadable?->type ?? 'Other'),
            'image_url' => $this->leadable?->getFirstMediaUrl('cover') ?: null,
        ];
    }

    private function mapDefault(): array
    {
        return [
            'type' => 'unknown',
            'title' => 'Unknown Entity',
            'subtitle' => '',
            'image_url' => null,
        ];
    }
}
