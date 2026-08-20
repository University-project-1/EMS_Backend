<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $types = [
            'App\\Notifications\\SystemUser\\Exhibitor\\BoothPaymentReminderNotification' => 'booth_payment_reminder',
            'App\\Notifications\\SystemUser\\Exhibitor\\EventPaymentReminderNotification' => 'event_payment_reminder',
            'App\\Notifications\\SystemUser\\Exhibitor\\BoothCancellationNotification' => 'booth_canceled',
        ];

        foreach ($types as $legacyType => $type) {
            DB::table('notifications')
                ->where('type', $legacyType)
                ->update(['type' => $type]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $types = [
            'booth_payment_reminder' => 'App\\Notifications\\SystemUser\\Exhibitor\\BoothPaymentReminderNotification',
            'event_payment_reminder' => 'App\\Notifications\\SystemUser\\Exhibitor\\EventPaymentReminderNotification',
            'booth_canceled' => 'App\\Notifications\\SystemUser\\Exhibitor\\BoothCancellationNotification',
        ];

        foreach ($types as $type => $legacyType) {
            DB::table('notifications')
                ->where('type', $type)
                ->update(['type' => $legacyType]);
        }
    }
};
