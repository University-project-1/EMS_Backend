<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_interest_notification_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->string('type');
            $table->timestamp('sent_at');
            $table->unique(
                ['user_id', 'notifiable_type', 'notifiable_id', 'type'],
                'lead_interest_delivery_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_interest_notification_deliveries');
    }
};
