<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('leadable');
            $table->timestamp('created_at');
            $table->unique(['user_id', 'leadable_type', 'leadable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
