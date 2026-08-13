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
    public function invite(Model $inviteable, SystemUser $sender, string $email): Invitation
    {
        $user = SystemUser::where('email', $email)->first();
        if ($user && $inviteable->systemUsers()->where('system_users.id', $user->id)->exists()) {
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
        DB::transaction(function () use ($invitation) {
            $lockedInvitation = Invitation::where('id', $invitation->id)->lockForUpdate()->first();

            if (! $lockedInvitation || ! $lockedInvitation->isValid()) {
                throw new HttpException(410, __('errors.invalid_or_expired_token'));
            }

            $user = SystemUser::where('email', $lockedInvitation->email)->first();
            if (! $user) {
                throw new HttpException(404, __('errors.user_not_found'));
            }

            $lockedInvitation->inviteable->systemUsers()->syncWithoutDetaching([
                $user->id => [
                    'assigned_by' => $lockedInvitation->sender_id,
                    'created_at' => now()
                ],
            ]);
            $lockedInvitation->update(['status' => Status::APPROVED]);
        });
    }

    public function reject(Invitation $invitation): void
    {
        $this->check($invitation);
        $invitation->update(['status' => Status::REJECTED]);
    }

    public function delete(Invitation $invitation): void
    {
        DB::transaction(function () use ($invitation) {
            if ($invitation->status === Status::APPROVED) {
                $invitedUser = SystemUser::where('email', $invitation->email)->first();

                if ($invitedUser && $invitation->inviteable) {
                    $invitation->inviteable->systemUsers()->detach($invitedUser->id);
                }
            }
            $invitation->delete();
        });
    }

    public function getInvitationByToken(string $token): Invitation
    {
        $invitation = Invitation::with(['sender', 'inviteable'])
            ->where('token', $token)
            ->first();

        if (! $invitation || ! $invitation->isValid()) {
            abort(410, __('errors.invalid_or_expired_token'));
        }

        return $invitation;
    }

    public function check(Invitation $invitation): bool
    {
        if (! $invitation->isValid()) {
            abort(410, __('errors.invalid_or_expired_token'));
        }

        return true;
    }
}
