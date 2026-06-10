<?php

namespace App\Console\Commands;

use App\Models\OtpCode;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cleanup:unverified')]
#[Description('Clean up expired OTPs, used OTPs')]
class CleanUnverifiedAccounts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cleanup OTP Table
        $deletedOtps = OtpCode::where('expires_at', '<', now())
            ->orWhere('is_used', true)
            ->delete();

        $this->info("Deleted {$deletedOtps} expired or used OTP records.");
    }
}
