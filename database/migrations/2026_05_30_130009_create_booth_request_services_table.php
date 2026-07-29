<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booth_request_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('request_id')->constrained('booth_requests')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);

            $table->unique(['request_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booth_request_services');
    }
};
