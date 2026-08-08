<?php

namespace App\Services\Shared;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationService
{
    /**
     * Get authenticated user according to the guard.
     */
    protected function user(string $guardName)
    {
        return auth($guardName)->user();
    }

    /**
     * Get notifications.
     */
    public function index(string $guardName, int $perPage)
    {
        $user = $this->user($guardName);

        $query = QueryBuilder::for($user->notifications())
                    ->allowedFilters(
                        AllowedFilter::exact('type'),
                    )
                    ->allowedSorts(
                        'created_at',
                    )
                    ->defaultSort('-created_at');

        if($guardName === 'mobile'){
            return $query->cursorPaginate($perPage);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get notification statistics.
     */
    public function statistics(string $guardName): array
    {
        $user = $this->user($guardName);

        $statistics = $user->notifications()
            ->selectRaw('COUNT(*) as total_notifications')
            ->selectRaw('SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread_notifications')
            ->selectRaw('SUM(CASE WHEN read_at IS NOT NULL THEN 1 ELSE 0 END) as read_notifications')->first();

        return [
            'total_notifications' => (int) $statistics->total_notifications,
            'unread_notifications' => (int) $statistics->unread_notifications,
            'read_notifications' => (int) $statistics->read_notifications,
        ];
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(string $guardName): void
    {
        $this->user($guardName)
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now(),]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(DatabaseNotification $notification,string $guardName): void {
        $user = $this->user($guardName);

        $user->notifications()
            ->whereKey($notification->getKey())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(DatabaseNotification $notification,string $guardName): void {
        $user = $this->user($guardName);

        $user->notifications()
            ->whereKey($notification->getKey())
            ->delete();
    }
}