<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->morphs('savedable');
            $table->timestamp('created_at');
            $table->unique(['user_id', 'savedable_type', 'savedable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved');
    }
};
