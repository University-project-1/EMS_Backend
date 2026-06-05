<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOtpWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $tries = 3;
    public $backoff = 30;
    protected $phone;
    protected $otp;
    public function __construct($phone, $otp)
    {
        $this->phone = $phone;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $instanceId = config('services.ultramsg.instance_id');
        $token = config('services.ultramsg.token');
        $response =
            Http::post("https://api.ultramsg.com/{$instanceId}/messages/chat", [
                'token' => $token,
                'to' => $this->phone,
                'body' => "your otp is {$this->otp} please do not shar with any one."
            ]);

        if ($response->failed()) {
            Log::error("UltraMsg Failed: " . $response->body());
            throw new \Exception("Failed to send WhatsApp message");
        }
    }
}
