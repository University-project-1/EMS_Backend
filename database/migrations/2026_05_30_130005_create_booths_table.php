<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booths', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls');
            $table->foreignId('company_id')->index()->nullable()->constrained('companies')->nullOnDelete();
            $table->string('qr_token')->nullable()->unique();
            $table->string('number');
            $table->float('area');
            $table->decimal('price', 10, 2);
            $table->string('svg_id')->nullable();
            $table->unique(['hall_id', 'number']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booths');
    }
};
