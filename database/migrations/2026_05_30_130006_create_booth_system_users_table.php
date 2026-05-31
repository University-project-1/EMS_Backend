<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booth_system_users', function (Blueprint $table): void {
            $table->foreignId('booth_id')->constrained('booths')->cascadeOnDelete();
            $table->foreignId('system_user_id')->constrained('system_users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('system_users')->nullOnDelete();
            $table->timestamp('created_at');
            $table->primary(['booth_id', 'system_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booth_system_users');
    }
};
