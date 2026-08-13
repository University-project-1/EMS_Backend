<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_halls', function (Blueprint $table): void {
            $table->id();
            $table->string('number')->unique();
            $table->float('area')->index();
            $table->string('svg_id')->nullable();
            $table->decimal('price_per_hour', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_halls');
    }
};
