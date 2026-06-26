<?php
namespace App\Services\SystemUser\Exhibitor;

use App\Enum\Status;
use App\Models\Invitation;
use App\Models\SystemUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvitationService
{
    public function __construct() {}

    public function create(Model $inviteable, string $email): Invitation
    {
        $user = SystemUser::where('email', $email)->first();
        if ($user && $inviteable->systemUsers()->where('system_user_id', $user->id)->exists()) {
            abort(400, __('teams.alreadyExist'));
        }

        $invitation = $inviteable->invitations()->create([
            'email' => $email,
            'token' => Str::random(20),
            'expires_at' => now()->addDays(2),
        ]);

        // notify...

        return $invitation;
    }

    public function approve(string $token): void
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', Status::PENDING)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$invitation) {
            abort(404, __('errors.invalid_or_expired_token'));
        }

        $user = SystemUser::where('email', $invitation->email)->first();

        if (!$user) {
            throw new HttpException(404, __('errors.user_not_found'));
            // abort(404, __('errors.user_not_found'));
        }
        DB::transaction(function () use ($invitation, $user) {

            $invitation->inviteable->systemUsers()->syncWithoutDetaching($user->id);

            $invitation->update(['status' => Status::APPROVED]);
        });
    }

    public function reject(string $token): void
    {
        $invitation = Invitation::where('token', $token)
            ->where('status', Status::PENDING)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$invitation) {
            abort(404, __('errors.invalid_or_expired_token'));
        }

        $invitation->update(['status' => Status::REJECTED]);
    }
}
