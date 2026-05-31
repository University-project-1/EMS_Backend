<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_system_users', function (Blueprint $table): void {
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('system_user_id')->constrained('system_users')->cascadeOnDelete();
            $table->primary(['company_id', 'system_user_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('company_system_users');
    }
};
