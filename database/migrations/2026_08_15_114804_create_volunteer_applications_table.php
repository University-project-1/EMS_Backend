<?php

use App\Enum\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_applications', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone', 20)->unique();
            $table->text('motivation');
            $table->text('education_or_occupation');
            $table->text('skills')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('privacy_consent_at');
            $table->string('status', 20)->default(Status::PENDING->value)->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('system_users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamp('whatsapp_notification_sent_at')->nullable();
            $table->timestamp('whatsapp_notification_failed_at')->nullable();
            $table->text('whatsapp_notification_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_applications');
    }
};
