<?php

namespace App\Http\Requests\SystemUser\Shared;

use App\Enum\BusinessSectors;
use Illuminate\Validation\Rule;

/**
 * Summary of CompanyRules
 * Rules for company to use in booths and events booking flow
 */
class CompanyRules
{
    public static function get(string $prefix = ''): array
    {
        $p = $prefix ? $prefix . '.' : '';
        $req = $prefix ? 'required_with:' . $prefix : 'required';

        return [
            $p . 'name' => [$req, 'string', 'max:255'],
            $p . 'business_sector' => [$req, 'string', 'max:255', Rule::enum(BusinessSectors::class)],
            $p . 'phone' => [$req, 'string', 'max:20'],
            $p . 'description' => [$req, 'string', 'max:1800'],
            $p . 'year_founded' => [$req, 'digits:4', 'integer', 'max:' . date('Y')],
            $p . 'social_links' => [$req, 'array', 'max:5'],
            $p . 'social_links.*' => ['required_with:' . $p . 'social_links', 'url'],
            $p . 'headquarters_lat' => [$req, 'nullable', 'numeric', 'between:-90,90'],
            $p . 'headquarters_lng' => [$req, 'nullable', 'numeric', 'between:-180,180'],
            $p . 'logo' => [$req, 'image', 'mimes:jpg,png,jpeg,webp', 'max:4096'],
            $p . 'gallery' => ['nullable', 'array', 'max:10'],
            $p . 'gallery.*' => ['image', 'mimes:jpg,png,jpeg,webp', 'max:4096'],
        ];
    }
}
