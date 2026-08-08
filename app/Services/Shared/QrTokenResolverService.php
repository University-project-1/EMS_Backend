<?php
namespace App\Services\Shared;

use App\Models\Booth;
use App\Models\Event;

class QrTokenResolverService{
    public function resolve(string $token){
        return match (substr($token, 0, 1)) {
            'B' => Booth::where('qr_token', $token)->firstOrFail(),
            'E' => Event::where('qr_token', $token)->firstOrFail(),
            default => abort(__('validation.invalid_qr')),
        };
    }
}
