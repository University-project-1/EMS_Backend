<?php

namespace App\Services\Shared;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class UltraMessageService
{
    /**
     * Send a plain WhatsApp chat message through the project's UltraMessage instance.
     *
     * @throws ConnectionException
     */
    public function sendChatMessage(string $phone, string $message): void
    {
        $instanceId = config('services.ultramsg.instance_id');
        $token = config('services.ultramsg.token');

        if (blank($instanceId) || blank($token)) {
            throw new RuntimeException('UltraMessage is not configured.');
        }

        $response = Http::timeout(10)
            ->retry(2, 300, throw: false)
            ->post("https://api.ultramsg.com/{$instanceId}/messages/chat", [
                'token' => $token,
                'to' => $phone,
                'body' => $message,
            ]);

        if ($response->failed()) {
            throw new RuntimeException("UltraMessage request failed with status {$response->status()}.");
        }
    }
}
