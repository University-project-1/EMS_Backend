<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booth_products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booth_request_id')->constrained('booth_requests')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->text('description');
            $table->unsignedInteger('sort_order');
            $table->timestamps();

            $table->index(['booth_request_id', 'sort_order']);
            $table->index(['booth_request_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booth_products');
    }
};
