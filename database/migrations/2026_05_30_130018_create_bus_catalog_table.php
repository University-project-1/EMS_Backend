<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_catalog', function (Blueprint $table): void {
            $table->id();
            $table->string('location');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_catalog');
    }
};
