<?php
namespace App\Http\Resources\SystemUser\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'period' => $this->resource['period'],
            'summary' => $this->resource['summary'],
            'trends' => $this->resource['trends'],
            'breakdowns' => $this->resource['breakdowns'],
        ];
    }
}
