<?php

namespace App\Services\SystemUser\Exhibitor;

use App\Enum\Status;
use App\Models\Invitation;
use App\Models\SystemUser;
use App\Notifications\SystemUser\Exhibitor\InvitationNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InvitationService
{
    public function __construct() {}

    public function invite(Model $inviteable, SystemUser $sender, string $email): Invitation
    {
        $user = SystemUser::where('email', $email)->first();
        if ($user && $inviteable->systemUsers()->where('system_user_id', $user->id)->exists()) {
            abort(400, __('teams.alreadyExist'));
        }

        $invitation = $inviteable->invitations()->create([
            'sender_id' => $sender->id,
            'email' => $email,
            'token' => Str::random(20),
            'expires_at' => now()->addDays(2),
        ]);

        if ($user) {
            $user->notify(new InvitationNotification($invitation));
        } else {
            Notification::route('mail', $email)->notify(new InvitationNotification($invitation));
        }

        return $invitation;
    }

    public function approve(Invitation $invitation): void
    {
        $user = SystemUser::where('email', $invitation->email)->first();

        if (! $user) {
            throw new HttpException(404, __('errors.user_not_found'));
        }
        DB::transaction(function () use ($invitation, $user) {
            $invitation->inviteable->systemUsers()->syncWithoutDetaching($user->id);
            $invitation->update(['status' => Status::APPROVED]);
        });
    }

    public function reject(Invitation $invitation): void
    {
        $invitation->update(['status' => Status::REJECTED]);
    }

    public function getInvitationByToken(string $token): Invitation
    {
        $invitation = Invitation::with(['sender', 'inviteable'])
            ->where('token', $token)
            ->where('status', Status::PENDING)
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitation) {
            abort(404, __('errors.invalid_or_expired_token'));
        }

        return $invitation;
    }
}
