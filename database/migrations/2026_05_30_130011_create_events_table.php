<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->morphs('eventable');
            $table->foreignId('event_hall_id')->constrained('event_halls');
            $table->string('title');
            $table->text('description');
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->string('type')->default('other');
            $table->string('status')->default('pending');
            $table->string('qr_token')->unique();
            $table->dateTime('start_at');
            $table->integer('duration');
            $table->dateTime('end_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'event_hall_id',
                'status',
                'start_at',
                'end_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
