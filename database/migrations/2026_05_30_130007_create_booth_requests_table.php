<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booth_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booth_id')->constrained('booths');
            $table->foreignId('company_id')->constrained('companies');
            $table->foreignId('system_user_id')->constrained('system_users');
            $table->decimal('final_price', 10, 2);
            $table->string('status')->default('pending')->index();
            $table->text('reason_for_booking')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['booth_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booth_requests');
    }
};
