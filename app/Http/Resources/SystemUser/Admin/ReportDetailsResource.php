<?php

namespace App\Http\Resources\SystemUser\Admin;

use App\Models\Booth;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'admin_notes' => $this->admin_notes,
            'reporter' => $this->whenLoaded('reporter', function(){
                return [
                    'id' => $this->reporter->id,
                    'name' => $this->reporter->first_name . ' ' . $this->reporter->last_name,
                ];
            }),
            'resolved_by' => $this->whenLoaded('resolvedBy', function(){
                return [
                    'id' => $this->resolvedBy->id,
                    'name' => $this->resolvedBy->name,
                ];
            }),
            'reportable' => $this->reportableResource(),
            'created_at' => $this->created_at,
        ];
    }

    private function reportableResource(): mixed
    {
        if ($this->reportable_type === Event::class) {
            return $this->whenLoaded('reportable', function(){
                return [
                    'id' => $this->reportable->id,
                    'title' => $this->reportable->title,
                ];
            });
        }

        if ($this->reportable_type === Booth::class) {
            return $this->whenLoaded('reportable', function(){
                return [
                    'id' => $this->reportable->id,
                    'number' => $this->reportable->number,
                ];
            });
        }

        return null;
    }
}
