<?php

namespace App\Services\Exhibitor;


use App\Models\SystemUser;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAuthService
{
    public function handleGoogleProviderToken(string $providerToken): array
    {
        try {
            /** @var \Laravel\Socialite\Two\GoogleProvider $provider */
            $provider = Socialite::driver('google');
            // Verify the token securely with Google servers
            $googleUser = $provider->userFromToken($providerToken);
        } catch (Exception $e) {
            throw new Exception('Invalid or expired Google Token.');
        }

        return DB::transaction(function () use ($googleUser) {
            $user = SystemUser::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Link account if registering via Google for the first time on an existing email
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ]);
                }
            } else {
                $user = SystemUser::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]);

                if ($googleUser->getAvatar()) {
                    $this->downloadAndAssignAvatar($user, $googleUser->getAvatar());
                }
            }

            $tokenResult = $user->createToken('exhibitor_token');

            return [
                'user'  => $user,
                'token' => $tokenResult->accessToken,
            ];
        });
    }
    private function downloadAndAssignAvatar($user, $avatarUrl)
    {
        try {
            $response = Http::get($avatarUrl);
            if ($response->successful()) {
                $physicalPath = storage_path('app/temp_google_avatar_' . $user->id . '.jpg');
                File::put($physicalPath, $response->body());
                $user->addMedia($physicalPath)->toMediaCollection('avatar');
            }
        } catch (Exception $e) {
            Log::error('Google Avatar Error: ' . $e->getMessage());
        }
    }
}
